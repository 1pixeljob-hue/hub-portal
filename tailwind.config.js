/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./index.html",
        "./src/**/*.{js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            colors: {
                "primary": "#6366f1",
                "secondary": "#ec4899",
                "accent": "#8b5cf6",
                "background-light": "#f8fafc",
                "background-dark": "#0f172a",
                "surface-light": "#ffffff",
                "surface-dark": "#1e293b",
                "surface-light-highlight": "#f1f5f9",
                "surface-dark-highlight": "#334155",
                "border-light": "#e2e8f0",
                "border-dark": "#334155",
                "text-primary-light": "#0f172a",
                "text-primary-dark": "#f8fafc",
                "text-secondary-light": "#64748b",
                "text-secondary-dark": "#94a3b8"
            },
            fontFamily: {
                "display": ["Inter", "sans-serif"]
            },
            backgroundImage: {
                'mesh-light': 'radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%)',
                'mesh-dark': 'radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,25%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%)',
                'mesh-vibrant': 'radial-gradient(at 40% 20%, hsla(266,100%,70%,0.15) 0px, transparent 50%), radial-gradient(at 80% 0%, hsla(189,100%,56%,0.15) 0px, transparent 50%), radial-gradient(at 0% 50%, hsla(340,100%,76%,0.15) 0px, transparent 50%)',
                'gradient-border': 'linear-gradient(to right, #6366f1, #a855f7, #ec4899)',
                'card-gradient': 'linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%)'
            },
        }
    },
    plugins: [],
}
