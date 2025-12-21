import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';
import AppLayout from '@/Layouts/AppLayout';
import { usePage } from '@inertiajs/react';

export default function Dashboard() {
    const auth = usePage().props.auth.user;

    return (
        <div className="flex w-full flex-col gap-y-4 pb-32">
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink href={route('dashboard')}>Cuan+</BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>Dashboard</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>

            <div
                className="flex flex-row items-center justify-between gap-2 rounded-xl bg-gradient-to-br
                    from-emerald-500 via-emerald-500 to-yellow-100 p-6 text-white"
            >
                <div className="flex flex-col">
                    <h2 className="text-2xl font-medium leading-relaxed">H1, {auth.name}</h2>
                    <p className="text-sm">
                        Selamat datang di <span className="font-bold">Cuan</span>, atur keuangan anda dengan baik demi
                        masa depan yang cerah.
                    </p>
                </div>
                <Avatar>
                    <AvatarImage src={auth.avatar} />
                    <AvatarFallback>{auth.name.substring(0, 1)}</AvatarFallback>
                </Avatar>
            </div>
        </div>
    );
}

Dashboard.layout = (page) => <AppLayout title="Dashboard" children={page} />;
