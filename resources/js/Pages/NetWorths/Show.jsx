import BreadcrumbHeader from '@/Components/BreadcrumbHeader';
import CardStat from '@/Components/CardStat';
import HeaderTitle from '@/Components/HeaderTitle';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import { Button } from '@/Components/ui/button';
import { Card, CardHeader } from '@/Components/ui/card';
import AppLayout from '@/Layouts/AppLayout';
import { formatToRupiah } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { IconArrowBack, IconCash, IconInfoCircle, IconMenorah } from '@tabler/icons-react';

export default function Show(props) {
    return (
        <div className="flex w-full flex-col gap-y-6 pb-32">
            <BreadcrumbHeader items={props.items} />
            <Card>
                <CardHeader className="p-0">
                    <div className="flex flex-col items-start justify-between gap-y-4 p-4 lg:flex-row lg:items-center">
                        <HeaderTitle
                            title={props.pageSettings.title}
                            subtitle={props.pageSettings.subtitle}
                            icon={IconMenorah}
                        />
                        <Button variant="emerald" size="xl" asChild>
                            <Link href={route('net-worths.index')}>
                                <IconArrowBack className="size-4" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
            </Card>
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <CardStat
                    data={{
                        title: 'Tujuan Kekayaan Bersih',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-orange-400 via-orange-500 to-orange-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.netWorth.net_worth_goal)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Kekayaan Bersih Saat Ini',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-blue-400 via-blue-500 to-blue-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.netWorth.current_net_worth)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Jumlah Yang Tersisa',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.netWorth.amount_left)}</div>
                </CardStat>
            </div>

            {/* alert */}
            <Alert variant="info">
                <IconInfoCircle className="size-6" />
                <AlertTitle>Aset</AlertTitle>
                <AlertDescription>
                    Aset adalah sesuatu yang dimiliki oleh individu atau perusahaan yang memiliki nilai ekonomi dan
                    dapat memberikan manfaat di masa depan. Contohnya: uang tunai, properti, investasi, atau peralatan.
                </AlertDescription>
            </Alert>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-5">
                <CardStat
                    data={{
                        title: 'Total Kas',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.assetSum.assetCashNominalSum)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Total Personal',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-orange-400 via-orange-500 to-orange-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.assetSum.assetPersonalNominalSum)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Total Investasi Jangka Pendek',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.assetSum.assetShortTermNominalSum)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Total Investasi Jangka Menengah',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-sky-400 via-sky-500 to-sky-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.assetSum.assetMidTermNominalSum)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Total Investasi Jangka Panjang',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-purple-400 via-purple-500 to-purple-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.assetSum.assetLongTermNominalSum)}</div>
                </CardStat>
            </div>
        </div>
    );
}

Show.layout = (page) => <AppLayout children={page} title={page.props.pageSettings.title} />;
