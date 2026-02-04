#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <MAX6675.h>
#include <ClearDS1302.h>
#include <EEPROM.h>

// ===== DS1302 RTC =====
#define RTC_RST 9
#define RTC_DAT 8
#define RTC_CLK 7

// ===== MAX6675 (AC TEMP) =====
#define AC_SO 4
#define AC_CS 5
#define AC_SCK 6

// ===== MAX6675 (ENGINE TEMP) =====
#define ENG_SO A0
#define ENG_CS A1
#define ENG_SCK A2

// ===== RELAYS (SSR) =====
#define FAN1 10  // AC fan 1
#define FAN2 11  // AC fan 2
#define FAN3 12  // Engine fan 1
#define FAN4 13  // Engine fan 2

// ===== INPUT / OUTPUT =====
#define MENU_BTN 2     // Enter/exit menu, confirm
#define UP_BTN 3       // Move up/increase value
#define DOWN_BTN 5     // Move down/decrease value
#define BUZZER 6
#define VOLT_PIN A3

// ================= DISPLAY =================
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_RESET -1
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

// ================= RTC =================
ClearDS1302 RTC(RTC_DAT, RTC_RST, RTC_CLK);

// ================= TEMPERATURE =================
MAX6675 acTemp(AC_SCK, AC_CS, AC_SO);
MAX6675 engTemp(ENG_SCK, ENG_CS, ENG_SO);

// ================= SETTINGS =================
float acThreshold = 70.0;
float engTemp1 = 70.0;
float engTemp2 = 80.0;

// ================= STATE =================
bool fanState[4] = { true, false, false, false };
bool blinkColon = false;
unsigned long lastRTCRead = 0;
unsigned long lastTempRead = 0;
unsigned long lastDisplayUpdate = 0;
bool oledOK = false;

// ================= MENU SYSTEM =================
enum MenuState {
  MAIN_SCREEN,
  SETTINGS_MENU,
  FAN_SETTINGS,
  TEMP_SETTINGS,
  TIME_SETTINGS,
  SYSTEM_INFO
};

MenuState currentMenu = MAIN_SCREEN;
int menuCursor = 0;
int subMenuCursor = 0;
bool editingValue = false;

// Menu items
const char* mainMenuItems[] = {"Fan Control", "Temp Settings", "Time Settings", "System Info"};
const int mainMenuItemCount = 4;

const char* fanMenuItems[] = {"Fan 1", "Fan 2", "Fan 3", "Fan 4", "Back"};
const int fanMenuItemCount = 5;

const char* tempMenuItems[] = {"AC Threshold", "ENG Temp 1", "ENG Temp 2", "Back"};
const int tempMenuItemCount = 4;

const char* timeMenuItems[] = {"Set Hour", "Set Minute", "Set Date", "Set Month", "Set Year", "Back"};
const int timeMenuItemCount = 6;

// ================= EEPROM =================
#define EE_AC 0
#define EE_E1 10
#define EE_E2 20

// ================= SERIAL =================
String serialBuffer = "";
bool serialCommandReady = false;

// ================= DEBOUNCE =================
unsigned long lastDebounceTime = 0;
#define DEBOUNCE_DELAY 50

// ================= RTC TIME STRUCT =================
struct RTC_Time {
  byte hour;
  byte minute;
  byte second;
  byte date;
  byte month;
  byte year;
};

// ================= I2C SCAN =================
void scanI2C() {
  Serial.println(F("Scanning I2C bus..."));
  byte error, address;
  int nDevices = 0;
  
  for(address = 1; address < 127; address++) {
    Wire.beginTransmission(address);
    error = Wire.endTransmission();
    
    if (error == 0) {
      Serial.print("I2C device found at address 0x");
      if (address < 16) Serial.print("0");
      Serial.print(address, HEX);
      Serial.println(" !");
      nDevices++;
    }
  }
  
  if (nDevices == 0) {
    Serial.println(F("No I2C devices found"));
  } else {
    Serial.println(F("Scan complete"));
  }
}

// ================= SETUP =================
void setup() {
  Serial.begin(115200);
  Serial.println(F("======================================"));
  Serial.println(F("       GAJAH CANGGIH SYSTEM"));
  Serial.println(F("       3-Button Control"));
  Serial.println(F("======================================"));
  
  // Initialize I2C with proper speed
  Wire.begin();
  Wire.setClock(400000); // Fast mode 400kHz
  
  // Scan I2C bus first
  scanI2C();
  
  // Initialize OLED with multiple address attempts
  Serial.println(F("Initializing OLED..."));
  
  // Try different I2C addresses
  byte addresses[] = {0x3C, 0x3D};
  oledOK = false;
  
  for (int i = 0; i < 2; i++) {
    Serial.print(F("Trying address 0x"));
    Serial.println(addresses[i], HEX);
    
    Wire.beginTransmission(addresses[i]);
    if (Wire.endTransmission() == 0) {
      Serial.print(F("Device found at 0x"));
      Serial.println(addresses[i], HEX);
      
      if (display.begin(SSD1306_SWITCHCAPVCC, addresses[i])) {
        Serial.println(F("OLED initialized successfully!"));
        oledOK = true;
        displayWelcome();
        break;
      } else {
        Serial.println(F("OLED init failed at this address"));
      }
    }
  }
  
  if (!oledOK) {
    Serial.println(F("ERROR: OLED not found at any address!"));
    Serial.println(F("Continuing without display..."));
    // Show error on serial
    Serial.println(F("Check:"));
    Serial.println(F("1. OLED power (VCC to 5V, GND to GND)"));
    Serial.println(F("2. I2C pins (SDA to pin 20, SCL to pin 21)"));
    Serial.println(F("3. I2C pull-up resistors (4.7kΩ to 5V)"));
  }
  
  // Initialize pins
  pinMode(FAN1, OUTPUT);
  pinMode(FAN2, OUTPUT);
  pinMode(FAN3, OUTPUT);
  pinMode(FAN4, OUTPUT);
  
  pinMode(MENU_BTN, INPUT_PULLUP);
  pinMode(UP_BTN, INPUT_PULLUP);
  pinMode(DOWN_BTN, INPUT_PULLUP);
  pinMode(BUZZER, OUTPUT);
  
  // Set initial fan states
  updateFanOutputs();
  
  // Load settings from EEPROM
  loadSettings();
  
  // Test buzzer
  beep(100);
  delay(100);
  beep(100);
  
  Serial.println(F("System Ready!"));
  Serial.println(F("Buttons: Menu | Up | Down"));
  Serial.print(F("OLED Status: "));
  Serial.println(oledOK ? "OK" : "NOT FOUND");
  printHelp();
}

void displayWelcome() {
  if (!oledOK) return;
  
  display.clearDisplay();
  display.setTextSize(2);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(10, 20);
  display.println(F("GAJAH"));
  display.setCursor(10, 40);
  display.println(F("CANGGIH"));
  display.display();
  delay(2000);
  display.clearDisplay();
  display.display();
}

// ================= RTC HELPER FUNCTIONS =================
RTC_Time getRTCTime() {
  RTC_Time dt;
  dt.second = RTC.get.time.second();
  dt.minute = RTC.get.time.minutes();
  
  String hourStr = RTC.get.time.hour();
  dt.hour = 0;
  for (int i = 0; i < hourStr.length(); i++) {
    if (isDigit(hourStr.charAt(i))) {
      dt.hour = dt.hour * 10 + (hourStr.charAt(i) - '0');
    } else {
      break;
    }
  }
  
  dt.date = RTC.get.time.date();
  dt.month = RTC.get.time.month();
  dt.year = RTC.get.time.year();
  
  return dt;
}

void setRTCTime(RTC_Time dt) {
  RTC.set.time.second(dt.second);
  RTC.set.time.minutes(dt.minute);
  RTC.set.time.hour(dt.hour);
  RTC.set.time.date(dt.date);
  RTC.set.time.month(dt.month);
  RTC.set.time.year(dt.year);
}

// ================= BUTTON HANDLING =================
void handleButtons() {
  static bool lastMenuBtn = HIGH;
  static bool lastUpBtn = HIGH;
  static bool lastDownBtn = HIGH;
  
  bool menuBtn = digitalRead(MENU_BTN);
  bool upBtn = digitalRead(UP_BTN);
  bool downBtn = digitalRead(DOWN_BTN);
  
  unsigned long now = millis();
  
  // Menu Button (Enter/Exit/Confirm)
  if (menuBtn != lastMenuBtn) {
    lastDebounceTime = now;
  }
  
  if ((now - lastDebounceTime) > DEBOUNCE_DELAY) {
    if (menuBtn == LOW && lastMenuBtn == HIGH) {
      beep(50);
      handleMenuButton();
    }
  }
  
  // Up Button (Increase/Move Up)
  if (upBtn != lastUpBtn) {
    lastDebounceTime = now;
  }
  
  if ((now - lastDebounceTime) > DEBOUNCE_DELAY) {
    if (upBtn == LOW && lastUpBtn == HIGH) {
      beep(30);
      handleUpButton();
    }
  }
  
  // Down Button (Decrease/Move Down)
  if (downBtn != lastDownBtn) {
    lastDebounceTime = now;
  }
  
  if ((now - lastDebounceTime) > DEBOUNCE_DELAY) {
    if (downBtn == LOW && lastDownBtn == HIGH) {
      beep(30);
      handleDownButton();
    }
  }
  
  lastMenuBtn = menuBtn;
  lastUpBtn = upBtn;
  lastDownBtn = downBtn;
}

void handleMenuButton() {
  if (currentMenu == MAIN_SCREEN) {
    currentMenu = SETTINGS_MENU;
    menuCursor = 0;
    editingValue = false;
    Serial.println(F("[BUTTON] Entered Menu"));
  }
  else if (currentMenu == SETTINGS_MENU) {
    switch(menuCursor) {
      case 0: currentMenu = FAN_SETTINGS; subMenuCursor = 0; break;
      case 1: currentMenu = TEMP_SETTINGS; subMenuCursor = 0; break;
      case 2: currentMenu = TIME_SETTINGS; subMenuCursor = 0; break;
      case 3: currentMenu = SYSTEM_INFO; break;
    }
    editingValue = false;
  }
  else if (currentMenu == FAN_SETTINGS) {
    if (subMenuCursor == 4) {
      currentMenu = SETTINGS_MENU;
      menuCursor = 0;
    } else {
      fanState[subMenuCursor] = !fanState[subMenuCursor];
      updateFanOutputs();
      Serial.print(F("[BUTTON] Fan "));
      Serial.print(subMenuCursor + 1);
      Serial.println(fanState[subMenuCursor] ? " ON" : " OFF");
    }
  }
  else if (currentMenu == TEMP_SETTINGS) {
    if (subMenuCursor == 3) {
      currentMenu = SETTINGS_MENU;
      menuCursor = 1;
    } else if (!editingValue) {
      editingValue = true;
      Serial.println(F("[BUTTON] Editing temperature"));
    } else {
      editingValue = false;
      saveSettings();
      Serial.println(F("[BUTTON] Temperature saved"));
    }
  }
  else if (currentMenu == TIME_SETTINGS) {
    if (subMenuCursor == 5) {
      currentMenu = SETTINGS_MENU;
      menuCursor = 2;
    } else if (!editingValue) {
      editingValue = true;
      Serial.println(F("[BUTTON] Editing time"));
    } else {
      editingValue = false;
      Serial.println(F("[BUTTON] Time saved"));
    }
  }
  else if (currentMenu == SYSTEM_INFO) {
    currentMenu = SETTINGS_MENU;
    menuCursor = 3;
  }
}

void handleUpButton() {
  if (editingValue) {
    if (currentMenu == TEMP_SETTINGS) {
      switch(subMenuCursor) {
        case 0: acThreshold += 0.5; if (acThreshold > 100.0) acThreshold = 30.0; break;
        case 1: engTemp1 += 0.5; if (engTemp1 > 120.0) engTemp1 = 50.0; break;
        case 2: engTemp2 += 0.5; if (engTemp2 > 120.0) engTemp2 = 60.0; break;
      }
    } else if (currentMenu == TIME_SETTINGS) {
      adjustTimeValue(1);
    }
  } else {
    if (currentMenu == SETTINGS_MENU) {
      menuCursor--;
      if (menuCursor < 0) menuCursor = mainMenuItemCount - 1;
    } else if (currentMenu == FAN_SETTINGS) {
      subMenuCursor--;
      if (subMenuCursor < 0) subMenuCursor = fanMenuItemCount - 1;
    } else if (currentMenu == TEMP_SETTINGS) {
      subMenuCursor--;
      if (subMenuCursor < 0) subMenuCursor = tempMenuItemCount - 1;
    } else if (currentMenu == TIME_SETTINGS) {
      subMenuCursor--;
      if (subMenuCursor < 0) subMenuCursor = timeMenuItemCount - 1;
    }
  }
}

void handleDownButton() {
  if (editingValue) {
    if (currentMenu == TEMP_SETTINGS) {
      switch(subMenuCursor) {
        case 0: acThreshold -= 0.5; if (acThreshold < 30.0) acThreshold = 100.0; break;
        case 1: engTemp1 -= 0.5; if (engTemp1 < 50.0) engTemp1 = 120.0; break;
        case 2: engTemp2 -= 0.5; if (engTemp2 < 60.0) engTemp2 = 120.0; break;
      }
    } else if (currentMenu == TIME_SETTINGS) {
      adjustTimeValue(-1);
    }
  } else {
    if (currentMenu == SETTINGS_MENU) {
      menuCursor = (menuCursor + 1) % mainMenuItemCount;
    } else if (currentMenu == FAN_SETTINGS) {
      subMenuCursor = (subMenuCursor + 1) % fanMenuItemCount;
    } else if (currentMenu == TEMP_SETTINGS) {
      subMenuCursor = (subMenuCursor + 1) % tempMenuItemCount;
    } else if (currentMenu == TIME_SETTINGS) {
      subMenuCursor = (subMenuCursor + 1) % timeMenuItemCount;
    }
  }
}

void adjustTimeValue(int delta) {
  RTC_Time now = getRTCTime();
  
  switch(subMenuCursor) {
    case 0:
      now.hour = (now.hour + delta + 24) % 24;
      break;
    case 1:
      now.minute = (now.minute + delta + 60) % 60;
      break;
    case 2:
      now.date = constrain(now.date + delta, 1, 31);
      break;
    case 3:
      now.month = constrain(now.month + delta, 1, 12);
      break;
    case 4:
      now.year = constrain(now.year + delta, 0, 99);
      break;
  }
  
  setRTCTime(now);
}

// ================= BEEP =================
void beep(int duration) {
  tone(BUZZER, 2000, duration);
}

// ================= EEPROM FUNCTIONS =================
void saveSettings() {
  EEPROM.put(EE_AC, acThreshold);
  EEPROM.put(EE_E1, engTemp1);
  EEPROM.put(EE_E2, engTemp2);
  beep(100);
  delay(50);
  beep(100);
}

void loadSettings() {
  EEPROM.get(EE_AC, acThreshold);
  EEPROM.get(EE_E1, engTemp1);
  EEPROM.get(EE_E2, engTemp2);
}

// ================= FAN CONTROL =================
void updateFanOutputs() {
  digitalWrite(FAN1, fanState[0]);
  digitalWrite(FAN2, fanState[1]);
  digitalWrite(FAN3, fanState[2]);
  digitalWrite(FAN4, fanState[3]);
}

bool controlFan(int fanNum, String cmd) {
  if (fanNum < 1 || fanNum > 4) return false;
  int idx = fanNum - 1;
  
  cmd.toUpperCase();
  if (cmd == "ON") {
    fanState[idx] = true;
  } else if (cmd == "OFF") {
    fanState[idx] = false;
  } else if (cmd == "TOGGLE") {
    fanState[idx] = !fanState[idx];
  } else {
    return false;
  }
  updateFanOutputs();
  return true;
}

String getFanStatus() {
  String status = "";
  for (int i = 0; i < 4; i++) {
    status += "F";
    status += (i + 1);
    status += ":";
    status += fanState[i] ? "ON" : "OFF";
    if (i < 3) status += " ";
  }
  return status;
}

void controlFansAuto(float ac, float eng) {
  if (currentMenu == MAIN_SCREEN) {
    fanState[1] = ac > acThreshold;
    fanState[2] = eng > engTemp1;
    fanState[3] = eng > engTemp2;
  }
  updateFanOutputs();
}

// ================= VOLTAGE =================
float readVoltage() {
  int raw = analogRead(VOLT_PIN);
  return raw * (5.0 / 1023.0) * 5.7;
}

// ================= TIME DISPLAY =================
String getTimeString() {
  RTC_Time dt = getRTCTime();
  
  String t = String(dt.hour);
  t += blinkColon ? ":" : " ";
  if (dt.minute < 10) t += "0";
  t += String(dt.minute);
  return t;
}

String getFullTimeString() {
  RTC_Time dt = getRTCTime();
  
  char timeStr[30];
  sprintf(timeStr, "%02d:%02d:%02d %02d/%02d/20%02d", 
          dt.hour, dt.minute, dt.second, dt.date, dt.month, dt.year);
  return String(timeStr);
}

// ================= ALARM =================
void checkAlarm(float eng) {
  static bool alarmOn = false;
  
  if (eng > engTemp2 + 10) {
    if (!alarmOn) {
      tone(BUZZER, 2000);
      alarmOn = true;
    }
  } else {
    if (alarmOn) {
      noTone(BUZZER);
      alarmOn = false;
    }
  }
}

// ================= DISPLAY FUNCTIONS =================
void drawMainScreen(float ac, float eng, float volt) {
  if (!oledOK) return;
  
  display.clearDisplay();
  
  // Time (large)
  display.setTextSize(2);
  display.setCursor(0, 0);
  display.print(getTimeString());
  
  // Data
  display.setTextSize(1);
  display.setCursor(0, 22);
  display.print("AC ");
  display.print(ac, 1);
  display.print("C");
  
  display.setCursor(0, 32);
  display.print("ENG ");
  display.print(eng, 1);
  display.print("C");
  
  display.setCursor(0, 42);
  display.print("V ");
  display.print(volt, 1);
  
  // Fan status
  display.setCursor(0, 54);
  display.print("F:");
  for (int i = 0; i < 4; i++) {
    display.print(fanState[i] ? "1" : "0");
  }
  
  // Menu hint
  display.setCursor(90, 54);
  display.print("MENU");
  
  display.display();
}

void drawSettingsMenu() {
  if (!oledOK) return;
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  display.setCursor(40, 0);
  display.print(F("SETTINGS"));
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
  
  for (int i = 0; i < mainMenuItemCount; i++) {
    if (i == menuCursor) {
      display.setTextColor(SSD1306_BLACK, SSD1306_WHITE);
      display.setCursor(0, 12 + i * 10);
      display.print("> ");
      display.println(mainMenuItems[i]);
      display.setTextColor(SSD1306_WHITE);
    } else {
      display.setCursor(0, 12 + i * 10);
      display.print("  ");
      display.println(mainMenuItems[i]);
    }
  }
  
  display.display();
}

void drawFanSettings() {
  if (!oledOK) return;
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  display.setCursor(40, 0);
  display.print(F("FAN CONTROL"));
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
  
  for (int i = 0; i < fanMenuItemCount; i++) {
    if (i == subMenuCursor) {
      display.setTextColor(SSD1306_BLACK, SSD1306_WHITE);
    }
    
    display.setCursor(0, 12 + i * 10);
    
    if (i < 4) {
      display.print("Fan ");
      display.print(i + 1);
      display.print(": ");
      display.print(fanState[i] ? "ON" : "OFF");
    } else {
      display.print("Back");
    }
    
    if (i == subMenuCursor) {
      display.setTextColor(SSD1306_WHITE);
    }
  }
  
  display.display();
}

void drawTempSettings() {
  if (!oledOK) return;
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  display.setCursor(40, 0);
  display.print(F("TEMPERATURE"));
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
  
  for (int i = 0; i < tempMenuItemCount; i++) {
    if (i == subMenuCursor) {
      display.setTextColor(SSD1306_BLACK, SSD1306_WHITE);
    }
    
    display.setCursor(0, 12 + i * 10);
    
    switch(i) {
      case 0:
        display.print("AC Threshold: ");
        display.print(acThreshold, 1);
        display.print("C");
        break;
      case 1:
        display.print("ENG Temp 1: ");
        display.print(engTemp1, 1);
        display.print("C");
        break;
      case 2:
        display.print("ENG Temp 2: ");
        display.print(engTemp2, 1);
        display.print("C");
        break;
      case 3:
        display.print("Back");
        break;
    }
    
    if (i == subMenuCursor) {
      display.setTextColor(SSD1306_WHITE);
    }
  }
  
  if (editingValue && subMenuCursor < 3) {
    display.setCursor(0, 55);
    display.print("Editing...");
  }
  
  display.display();
}

void drawTimeSettings() {
  if (!oledOK) return;
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  display.setCursor(50, 0);
  display.print(F("TIME"));
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
  
  RTC_Time dt = getRTCTime();
  
  for (int i = 0; i < timeMenuItemCount; i++) {
    if (i == subMenuCursor) {
      display.setTextColor(SSD1306_BLACK, SSD1306_WHITE);
    }
    
    display.setCursor(0, 12 + i * 8);
    
    switch(i) {
      case 0:
        display.print("Hour: ");
        if (dt.hour < 10) display.print("0");
        display.print(dt.hour);
        break;
      case 1:
        display.print("Minute: ");
        if (dt.minute < 10) display.print("0");
        display.print(dt.minute);
        break;
      case 2:
        display.print("Date: ");
        if (dt.date < 10) display.print("0");
        display.print(dt.date);
        break;
      case 3:
        display.print("Month: ");
        if (dt.month < 10) display.print("0");
        display.print(dt.month);
        break;
      case 4:
        display.print("Year: 20");
        if (dt.year < 10) display.print("0");
        display.print(dt.year);
        break;
      case 5:
        display.print("Back");
        break;
    }
    
    if (i == subMenuCursor) {
      display.setTextColor(SSD1306_WHITE);
    }
  }
  
  if (editingValue && subMenuCursor < 5) {
    display.setCursor(0, 55);
    display.print("Editing...");
  }
  
  display.display();
}

void drawSystemInfo() {
  if (!oledOK) return;
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  
  display.setCursor(40, 0);
  display.print(F("SYSTEM INFO"));
  display.drawLine(0, 10, 128, 10, SSD1306_WHITE);
  
  display.setCursor(0, 15);
  display.print("GAJAH CANGGIH v2.0");
  
  display.setCursor(0, 25);
  display.print("Arduino Mega 2560");
  
  display.setCursor(0, 35);
  display.print("3-Button Control");
  
  display.setCursor(0, 45);
  display.print("OLED: ");
  display.print(oledOK ? "OK" : "NOT FOUND");
  
  display.display();
}

// ================= SERIAL COMMANDS =================
void printHelp() {
  Serial.println(F("\n=== SERIAL COMMANDS ==="));
  Serial.println(F("  TIME           - Get current date/time"));
  Serial.println(F("  DATA           - Get all sensor data"));
  Serial.println(F("  FANS           - Get fan status"));
  Serial.println(F("  FANx ON/OFF/T  - Control fan (x=1-4)"));
  Serial.println(F("  SETTINGS       - Show temperature settings"));
  Serial.println(F("  SETAC X.X      - Set AC threshold (°C)"));
  Serial.println(F("  SETENG1 X.X    - Set Engine temp1 (°C)"));
  Serial.println(F("  SETENG2 X.X    - Set Engine temp2 (°C)"));
  Serial.println(F("  SAVE           - Save to EEPROM"));
  Serial.println(F("  I2CSCAN        - Scan I2C bus"));
  Serial.println(F("  STATUS         - System status"));
  Serial.println(F("  HELP           - Show this help"));
  Serial.println(F("==========================\n"));
}

void processSerialCommand() {
  if (!serialCommandReady) return;
  
  String cmd = serialBuffer;
  cmd.trim();
  cmd.toUpperCase();
  serialBuffer = "";
  serialCommandReady = false;
  
  if (cmd.length() == 0) return;
  
  Serial.print("> ");
  Serial.println(cmd);
  
  if (cmd == "HELP") {
    printHelp();
  }
  else if (cmd == "TIME") {
    Serial.print("Current time: ");
    Serial.println(getFullTimeString());
  }
  else if (cmd == "DATA") {
    float ac = acTemp.getCelsius();
    float eng = engTemp.getCelsius();
    float volt = readVoltage();
    
    Serial.println(F("=== SYSTEM DATA ==="));
    Serial.print(F("Time:     ")); Serial.println(getFullTimeString());
    Serial.print(F("AC Temp:  ")); Serial.print(ac, 1); Serial.println("°C");
    Serial.print(F("ENG Temp: ")); Serial.print(eng, 1); Serial.println("°C");
    Serial.print(F("Voltage:  ")); Serial.print(volt, 1); Serial.println("V");
    Serial.print(F("Fans:     ")); Serial.println(getFanStatus());
    Serial.print(F("Settings: AC=")); Serial.print(acThreshold, 1);
    Serial.print(F("°C, ENG1=")); Serial.print(engTemp1, 1);
    Serial.print(F("°C, ENG2=")); Serial.print(engTemp2, 1); Serial.println("°C");
    Serial.println(F("==================="));
  }
  else if (cmd == "FANS") {
    Serial.print("Fan Status: ");
    Serial.println(getFanStatus());
  }
  else if (cmd.startsWith("FAN")) {
    int fanNum = cmd.charAt(3) - '0';
    if (fanNum >= 1 && fanNum <= 4) {
      String action = cmd.substring(5);
      if (controlFan(fanNum, action)) {
        Serial.print("Fan ");
        Serial.print(fanNum);
        Serial.println(fanState[fanNum-1] ? " turned ON" : " turned OFF");
      } else {
        Serial.println(F("ERROR: Use ON, OFF, or TOGGLE"));
      }
    } else {
      Serial.println(F("ERROR: Fan number must be 1-4"));
    }
  }
  else if (cmd == "SETTINGS") {
    Serial.println(F("=== TEMPERATURE SETTINGS ==="));
    Serial.print(F("AC Threshold:    ")); Serial.print(acThreshold, 1); Serial.println("°C");
    Serial.print(F("Engine Temp 1:   ")); Serial.print(engTemp1, 1); Serial.println("°C");
    Serial.print(F("Engine Temp 2:   ")); Serial.print(engTemp2, 1); Serial.println("°C");
    Serial.println(F("============================"));
  }
  else if (cmd.startsWith("SETAC ")) {
    float val = cmd.substring(6).toFloat();
    if (val >= 30.0 && val <= 100.0) {
      acThreshold = val;
      Serial.print("AC threshold set to: ");
      Serial.print(acThreshold, 1);
      Serial.println("°C");
    } else {
      Serial.println(F("ERROR: Must be 30.0-100.0°C"));
    }
  }
  else if (cmd.startsWith("SETENG1 ")) {
    float val = cmd.substring(8).toFloat();
    if (val >= 50.0 && val <= 120.0) {
      engTemp1 = val;
      Serial.print("Engine temp 1 set to: ");
      Serial.print(engTemp1, 1);
      Serial.println("°C");
    } else {
      Serial.println(F("ERROR: Must be 50.0-120.0°C"));
    }
  }
  else if (cmd.startsWith("SETENG2 ")) {
    float val = cmd.substring(8).toFloat();
    if (val >= 60.0 && val <= 120.0 && val > engTemp1) {
      engTemp2 = val;
      Serial.print("Engine temp 2 set to: ");
      Serial.print(engTemp2, 1);
      Serial.println("°C");
    } else {
      Serial.println(F("ERROR: Must be 60.0-120.0°C and > ENG1"));
    }
  }
  else if (cmd == "SAVE") {
    saveSettings();
    Serial.println(F("Settings saved to EEPROM"));
  }
  else if (cmd == "I2CSCAN") {
    scanI2C();
  }
  else if (cmd == "STATUS") {
    Serial.println(F("=== SYSTEM STATUS ==="));
    Serial.print(F("OLED:          ")); Serial.println(oledOK ? "OK" : "NOT FOUND");
    Serial.print(F("Current Menu:  "));
    switch(currentMenu) {
      case MAIN_SCREEN: Serial.println("Main Screen"); break;
      case SETTINGS_MENU: Serial.println("Settings Menu"); break;
      case FAN_SETTINGS: Serial.println("Fan Settings"); break;
      case TEMP_SETTINGS: Serial.println("Temp Settings"); break;
      case TIME_SETTINGS: Serial.println("Time Settings"); break;
      case SYSTEM_INFO: Serial.println("System Info"); break;
    }
    Serial.print(F("Editing:       ")); Serial.println(editingValue ? "YES" : "NO");
    Serial.println(F("====================="));
  }
  else {
    Serial.println(F("ERROR: Unknown command. Type HELP for list."));
  }
}

// ================= READ SERIAL =================
void readSerial() {
  while (Serial.available()) {
    char c = Serial.read();
    if (c == '\n' || c == '\r') {
      serialCommandReady = true;
      break;
    } else {
      serialBuffer += c;
      if (serialBuffer.length() > 100) serialBuffer = "";
    }
  }
}

// ================= MAIN LOOP =================
void loop() {
  readSerial();
  
  if (serialCommandReady) {
    processSerialCommand();
  }
  
  handleButtons();
  
  if (millis() - lastRTCRead >= 500) {
    blinkColon = !blinkColon;
    lastRTCRead = millis();
  }
  
  if (millis() - lastTempRead >= 2000) {
    float ac = acTemp.getCelsius();
    float eng = engTemp.getCelsius();
    float volt = readVoltage();
    
    controlFansAuto(ac, eng);
    checkAlarm(eng);
    
    if (millis() - lastDisplayUpdate >= 500) {
      switch(currentMenu) {
        case MAIN_SCREEN:
          drawMainScreen(ac, eng, volt);
          break;
        case SETTINGS_MENU:
          drawSettingsMenu();
          break;
        case FAN_SETTINGS:
          drawFanSettings();
          break;
        case TEMP_SETTINGS:
          drawTempSettings();
          break;
        case TIME_SETTINGS:
          drawTimeSettings();
          break;
        case SYSTEM_INFO:
          drawSystemInfo();
          break;
      }
      lastDisplayUpdate = millis();
    }
    
    lastTempRead = millis();
  }
}