/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // Palet warna sesuai System Design — Islami & Trustworthy
                primary: {
                    DEFAULT: '#1B4332',
                    light: '#2D6A4F',
                },
                accent: {
                    DEFAULT: '#40916C',
                    light: '#74C69D',
                },
                gold: '#D4A017',
                surface: {
                    DEFAULT: '#F0FFF4',
                    2: '#D8F3DC',
                },
                ink: '#1A1A2E',
                muted: '#6B7280',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
            },
        },
    },
    plugins: [],
};
