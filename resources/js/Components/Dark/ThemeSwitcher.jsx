import { Button } from '@/Components/ui/button';
import { IconMoon, IconSun } from '@tabler/icons-react';
import { useTheme } from './ThemeProvider';

export default function ThemeSwitcher() {
    const { theme, setTheme } = useTheme();

    const toggleTheme = () => {
        // Cukup panggil setTheme saja.
        // ThemeProvider akan otomatis mengurus localStorage dan class 'dark' di HTML tag.
        setTheme(theme === 'dark' ? 'light' : 'dark');
    };

    return (
        <Button variant="emerald" size="xl" className="ml-auto" onClick={toggleTheme}>
            {theme === 'dark' ? <IconSun className="size-10 text-white" /> : <IconMoon className="size-10" />}
        </Button>
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
