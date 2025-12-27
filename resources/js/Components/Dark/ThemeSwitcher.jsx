
import { IconMoon, IconSun } from '@tabler/icons-react';
import { useTheme } from './ThemeProvider';

export default function ThemeSwitcher() {
    const { theme, setTheme } = useTheme();

    const toggleTheme = () => {
        setTheme(theme === 'dark' ? 'light' : 'dark');
    };

    return (
        <button
            onClick={toggleTheme}
            className={`relative inline-flex h-9 w-16 items-center rounded-full transition-colors duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 ${theme === 'dark' ? 'bg-gray-700' : 'bg-emerald-100'
                }`}
        >
            <span className="sr-only">Toggle Theme</span>

            {/* Sun Icon (Hidden in Dark Mode) */}
            <div className="absolute left-1.5 flex items-center justify-center text-emerald-600 transition-opacity duration-300">
                <IconSun size={16} className={theme === 'dark' ? 'opacity-0' : 'opacity-100'} />
            </div>

            {/* Moon Icon (Hidden in Light Mode) */}
            <div className="absolute right-1.5 flex items-center justify-center text-emerald-400 transition-opacity duration-300">
                <IconMoon size={16} className={theme === 'dark' ? 'opacity-100' : 'opacity-0'} />
            </div>

            {/* Sliding Knob */}
            <span
                className={`${theme === 'dark' ? 'translate-x-8 bg-gray-900' : 'translate-x-1 bg-white shadow-sm'
                    } flex h-7 w-7 items-center justify-center rounded-full transform transition-transform duration-300 ease-in-out`}
            >
                {theme === 'dark' ? (
                    <IconMoon size={14} className="text-emerald-400" />
                ) : (
                    <IconSun size={14} className="text-emerald-500" />
                )}
            </span>
        </button>
    );
}

// import { Button } from '@/Components/ui/button';
// import { IconMoon, IconSun } from '@tabler/icons-react';
// import { useTheme } from './ThemeProvider';

// export default function ThemeSwitcher() {
//     const { theme, setTheme } = useTheme();

//     const toggleTheme = () => {
//         if (theme === 'dark') {
//             setTheme('light');
//             document.documentElement.classList.remove('dark');
//             localStorage.setTheme('theme', 'light');
//         } else {
//             setTheme('dark');
//             document.documentElement.classList.remove('dark');
//             localStorage.setTheme('theme', 'dark');
//         }
//     };

//     return (
//         <Button variant="emerald" size="xl" className="ml-auto" onClick={toggleTheme}>
//             {theme === 'dark' ? <IconSun className="size-10 text-white" /> : <IconMoon className="size-10" />}
//         </Button>
//     );
// }
