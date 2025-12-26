import CardStatTwo from '@/Components/CardStatTwo';
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
import { formatToRupiah } from '@/lib/utils';
import { usePage } from '@inertiajs/react';
import { IconDoorEnter, IconDoorExit, IconMenorah, IconMoneybag } from '@tabler/icons-react';

export default function Dashboard(props) {
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

            <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <div className="col-span-8 space-y-6">
                    <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        {/* Pemasukan */}
                        <div className="col-span-1">
                            <CardStatTwo
                                data={{
                                    title: 'Pemasukan',
                                    description: 'Total pemasukan yang diterima pada tahun ini',
                                    icon: IconDoorEnter,
                                    background: 'text-white bg-gradient-to-r from-blue-400 via-blue-500 to-blue-500',
                                    iconClassName: 'text-white',
                                }}
                            >
                                <div className="ml-12 text-2xl font-bold">{formatToRupiah(props.sum.incomeSum)}</div>
                            </CardStatTwo>
                        </div>

                        {/* Pengeluaran */}
                        <div className="col-span-1">
                            <CardStatTwo
                                data={{
                                    title: 'Pengeluaran',
                                    description: 'Total pengeluaran pada tahun ini',
                                    icon: IconDoorExit,
                                    background: 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-500',
                                    iconClassName: 'text-white',
                                }}
                            >
                                <div className="ml-12 text-2xl font-bold">{formatToRupiah(props.sum.expenseSum)}</div>
                            </CardStatTwo>
                        </div>

                        {/* Tabungan */}
                        <div className="col-span-1">
                            <CardStatTwo
                                data={{
                                    title: 'Tabungan',
                                    description: 'Total tabungan yang tersedia tahun ini',
                                    icon: IconMoneybag,
                                    background:
                                        'text-white bg-gradient-to-r from-orange-400 via-orange-500 to-orange-500',
                                    iconClassName: 'text-white',
                                }}
                            >
                                <div className="ml-12 text-2xl font-bold">{formatToRupiah(props.sum.balanceSum)}</div>
                            </CardStatTwo>
                        </div>

                        {/* Kekayaan Bersih */}
                        <div className="col-span-1">
                            <CardStatTwo
                                data={{
                                    title: 'Total Kekayaan Bersih',
                                    description: 'Total kekayaan bersih pada tahun ini',
                                    icon: IconMenorah,
                                    background: 'text-white bg-gradient-to-r from-sky-400 via-sky-500 to-sky-500',
                                    iconClassName: 'text-white',
                                }}
                            >
                                <div className="ml-12 text-2xl font-bold">
                                    {formatToRupiah(props.sum.netWorthSum)}
                                    <span className="text-xs"> (aset-kewajiban)</span>
                                </div>
                            </CardStatTwo>
                        </div>
                    </div>
                </div>
                <div className="col-span-4"></div>
            </div>
        </div>
    );
}

Dashboard.layout = (page) => <AppLayout title="Dashboard" children={page} />;
