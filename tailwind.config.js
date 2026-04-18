/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#F5F3FF',
                    100: '#EDE9FE',
                    200: '#DDD6FE',
                    300: '#C4B5FD',
                    400: '#A78BFA',
                    500: '#8B5CF6',
                    600: '#7C3AED',
                    700: '#6D28D9',
                    800: '#5B21B6',
                    900: '#4C1D95',
                },
                success: {
                    base: '#10B981',
                    dark: '#059669',
                },
                warning: {
                    base: '#F59E0B',
                    dark: '#D97706',
                },
                error: {
                    base: '#EF4444',
                    dark: '#DC2626',
                },
                info: {
                    base: '#3B82F6',
                    dark: '#2563EB',
                },
                neutral: {
                    bg: '#F9FAFB',
                    card: '#FFFFFF',
                    primary: '#111827',
                    secondary: '#4B5563',
                    border: '#E5E7EB',
                }
            },
            fontFamily: {
                display: ['"Bebas Neue"', 'cursive'],
                sans: ['"IBM Plex Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace'],
            },
            fontSize: {
                'display-h1': ['64px', { lineHeight: '1.1', fontWeight: '700' }],
                'title-1': ['32px', { lineHeight: '1.2', fontWeight: '600' }],
                'title-2': ['24px', { lineHeight: '1.3', fontWeight: '600' }],
                'title-3': ['20px', { lineHeight: '1.4', fontWeight: '600' }],
                'body-g': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
                'body-m': ['14px', { lineHeight: '1.5', fontWeight: '400' }],
                'legend': ['11px', { lineHeight: '1.4', fontWeight: '500' }],
            },
            borderRadius: {
                'lg': '8px',
            },
            boxShadow: {
                'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
            },
            spacing: {
                '4': '4px',
                '8': '8px',
                '12': '12px',
                '16': '16px',
                '24': '24px',
                '32': '32px',
                '48': '48px',
                '64': '64px',
            }
        },
    },
    plugins: [],
}
