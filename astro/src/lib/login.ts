import Swal from "sweetalert2";
import { loginUser } from "./api.js";

const canvas = document.getElementById("particles") as HTMLCanvasElement | null;
const ctx = canvas?.getContext("2d") ?? null;

type Particle = {
  x: number;
  y: number;
  vx: number;
  vy: number;
  size: number;
  opacity: number;
};

const particles: Particle[] = [];

function resizeCanvas() {
  if (!canvas) return;
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}

function createParticles() {
  if (!canvas) return;
  particles.length = 0;

  const count = Math.min(80, Math.max(35, Math.floor(window.innerWidth / 20)));
  for (let i = 0; i < count; i++) {
    particles.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      vx: (Math.random() - 0.5) * 0.45,
      vy: (Math.random() - 0.5) * 0.45,
      size: Math.random() * 2 + 1,
      opacity: Math.random() * 0.45 + 0.15,
    });
  }
}

function animateParticles() {
  if (!canvas || !ctx) return;

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  for (const particle of particles) {
    particle.x += particle.vx;
    particle.y += particle.vy;

    if (particle.x < 0 || particle.x > canvas.width) particle.vx *= -1;
    if (particle.y < 0 || particle.y > canvas.height) particle.vy *= -1;

    ctx.beginPath();
    ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(255, 255, 255, ${particle.opacity})`;
    ctx.fill();
  }

  window.requestAnimationFrame(animateParticles);
}

function setLoading(isLoading: boolean) {
  const button = document.getElementById("loginButton") as HTMLButtonElement | null;
  if (!button) return;

  button.disabled = isLoading;
  button.style.opacity = isLoading ? "0.75" : "1";
  button.querySelector("span")!.textContent = isLoading ? "Signing in..." : "Sign In";
}

function storeSession(result: any, username: string) {
  const token = result.token ?? result.data?.token ?? result.jwt ?? result.data?.jwt;
  const user = result.user ?? result.data?.user ?? { username };

  if (!token) {
    throw new Error("Login succeeded, but no token was returned.");
  }

  localStorage.setItem("jwt", token);
  localStorage.setItem("user", JSON.stringify(user));
  localStorage.setItem("lastLogin", new Date().toISOString());
  localStorage.setItem("jwt_expires", String(Date.now() + 24 * 60 * 60 * 1000));
}

function bindPasswordToggle() {
  const password = document.getElementById("password") as HTMLInputElement | null;
  const toggle = document.getElementById("togglePassword") as HTMLButtonElement | null;

  toggle?.addEventListener("click", () => {
    if (!password) return;
    password.type = password.type === "password" ? "text" : "password";
  });
}

function bindLoginForm() {
  const form = document.getElementById("loginForm") as HTMLFormElement | null;
  const username = document.getElementById("username") as HTMLInputElement | null;
  const password = document.getElementById("password") as HTMLInputElement | null;

  form?.addEventListener("submit", async (event) => {
    event.preventDefault();

    const usernameValue = username?.value.trim() ?? "";
    const passwordValue = password?.value ?? "";

    if (!usernameValue || !passwordValue) {
      await Swal.fire({
        icon: "warning",
        title: "Missing credentials",
        text: "Please enter your username and password.",
      });
      return;
    }

    try {
      setLoading(true);
      const result = await loginUser(usernameValue, passwordValue);
      storeSession(result, usernameValue);

      await Swal.fire({
        icon: "success",
        title: "Signed in",
        text: "Redirecting to your dashboard.",
        timer: 900,
        showConfirmButton: false,
      });

      window.location.href = "/";
    } catch (error) {
      await Swal.fire({
        icon: "error",
        title: "Login failed",
        text: error instanceof Error ? error.message : "Please check your credentials and try again.",
      });
    } finally {
      setLoading(false);
    }
  });
}

if (canvas && ctx) {
  resizeCanvas();
  createParticles();
  animateParticles();
  window.addEventListener("resize", () => {
    resizeCanvas();
    createParticles();
  });
}

bindPasswordToggle();
bindLoginForm();
