import { Avatar, AvatarFallback } from "@/Components/ui/avatar";
import { Dialog, Transition } from "@headlessui/react";
import { Head, Link } from "@inertiajs/react";
import { IconLayoutSidebar, IconX } from "@tabler/icons-react";
import { Fragment, useState } from "react";

export default function AppLayout({title, children}) {

    const [sidebarOpen, setSidebarOpen] = useState(false);

    return(
        <>
            <Head title={title}/>
            <div>
                <Transition.Root show={sidebarOpen} as={Fragment}>
                    <Dialog as='div' className='relative z-50 lg:hidden' onClose={setSidebarOpen}>

                        <Transition.Child
                            as={Fragment}
                            enter="transition-opacity ease-linear duration-300"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="transition-opacity ease-linear duration-300"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <div className="fixed inset-0 bg-gray-900/80"/>
                        </Transition.Child>

                        <div className="fixed inset-0 flex">
                            <Transition.Child
                                as={Fragment}
                                enter="transition ease-in-out duration-300 transform"
                                enterFrom="-trasnlate-x-full"
                                enterTo="translate-x-0"
                                leave="transition ease-in-out duration-300 transform"
                                leaveFrom="translate-x-0"
                                leaveTo="-translate-x-full"
                            >

                                <Dialog.Panel className='relative flex flex-1 w-full
                                max-w-sm mr-16'>
                                    <Transition.Child
                                        as={Fragment}
                                        enter="ease-in-out duration-300"
                                        enterFrom="opacity-0"
                                        enterTo="opacity-100"
                                        leave="ease-in-out duration-300"
                                        leaveFrom="opacity-100"
                                        leaveTo="opacity-0"
                                    >
                                        <div className="absolute top-0 flex justify-center w-16 pt-5 left-full">
                                            <button
                                                type="button"
                                                className="-m-2.5 p-2.5"
                                                onClick={() => setSidebarOpen(false)}
                                            >
                                                <IconX className="text-white size-6"/>
                                            </button>
                                        </div>

                                    </Transition.Child>

                                    <div className="flex flex-col px-6 pb-2 overflow-y-auto
                                    bg-white grow gap-y-5 dark:bg-background">
                                        {/* Sidebar */}
                                    </div>

                                </Dialog.Panel>

                            </Transition.Child>
                        </div>

                    </Dialog>
                </Transition.Root>

                <div className="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
                    <div className="flex flex-col px-4 overflow-y-auto grow gap-y-5 bg-slate-50 dark:boder-r
                    dark:border-r-card dark:bg-background">
                        {/* Sidebar */}
                    </div>
                </div>

                <div className="sticky top-0 z-40 flex items-center p-4 bg-white shadow-sm gap-x-6
                dark:bg-backgrouond sm:px-6 lg:hidden">

                    <button
                        type="button"
                        className="m-2.5 p-2.5 text-gray-700 lg:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <IconLayoutSidebar className="size-6"/>
                    </button>

                    <div className="flex-1 text-sm font-semibold leading-6 text-foreground">
                        {title}
                    </div>

                    <Link href='#'>
                        <Avatar>
                            <AvatarFallback>X</AvatarFallback>
                        </Avatar>
                    </Link>

                </div>

                <main className="py-4 dark:bg-background lg:pl-72">
                    <div className="px-4">
                        {children}
                    </div>
                </main>

            </div>
        </>
    )
}