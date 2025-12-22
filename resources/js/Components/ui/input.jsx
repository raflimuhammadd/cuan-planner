import { cn } from '@/lib/utils';
import * as React from 'react';

// 1. Tambahkan 'isFocused' pada destructuring parameter
const Input = React.forwardRef(({ className, type, isFocused, ...props }, ref) => {
    // Setup ref internal agar fitur auto-focus bisa jalan
    const localRef = React.useRef(null);
    const inputRef = ref || localRef;

    // 2. Tambahkan logika untuk menangani auto-focus
    React.useEffect(() => {
        if (isFocused) {
            inputRef.current.focus();
        }
    }, [isFocused]);

    return (
        <input
            type={type}
            className={cn(
                `flex h-12 w-full rounded-xl border border-input bg-transparent px-3 py-1 text-base text-foreground
                shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium
                file:text-foreground placeholder:text-muted-foreground focus-visible:border-none
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500
                disabled:cursor-not-allowed disabled:opacity-50 dark:text-white dark:[color-scheme:dark] md:text-sm`,
                className,
            )}
            // Gunakan ref gabungan tadi
            ref={inputRef}
            // 3. props di sini sudah BERSIH (tidak mengandung isFocused lagi)
            {...props}
        />
    );
});

Input.displayName = 'Input';

export { Input };

// import * as React from 'react';

// import { cn } from '@/lib/utils';

// const Input = React.forwardRef(({ className, type, ...props }, ref) => {
//     return (
//         <input
//             type={type}
//             className={cn(
//                 'flex h-12 w-full rounded-lg border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:border-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
//                 className,
//             )}
//             ref={ref}
//             {...props}
//         />
//     );
// });
// Input.displayName = 'Input';

// export { Input };
