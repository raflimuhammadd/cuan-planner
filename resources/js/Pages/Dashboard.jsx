import CardStatTwo from '@/Components/CardStatTwo';
import BarChartCustom from '@/Components/Chart/BarChartCustom';
import PieChartCustom from '@/Components/Chart/PieChartCustom';
import EmptyState from '@/Components/EmptyState';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';

import { Progress } from '@/Components/ui/progress';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/Layouts/AppLayout';
import { formatToRupiah } from '@/lib/utils';
import { usePage } from '@inertiajs/react';
import { IconDoorEnter, IconDoorExit, IconMenorah, IconMoneybag } from '@tabler/icons-react';
import { useState } from 'react';

function ExpandableList({ items, renderItem, emptyState, limit = 1, className = "space-y-4" }) {
    const [isExpanded, setIsExpanded] = useState(false);

    if (items.length === 0) {
        return emptyState;
    }

    const displayedItems = isExpanded ? items : items.slice(0, limit);

    return (
        <div className="space-y-2">
            <div className={className}>
                {displayedItems.map((item, index) => renderItem(item, index))}
            </div>
            {items.length > limit && (
                <button
                    onClick={() => setIsExpanded(!isExpanded)}
                    className="w-full text-center text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mt-2 hover:underline transition-all py-2"
                >
                    {isExpanded ? 'Sembunyikan' : 'Lihat Semua'}
                </button>
            )}
        </div>
    );
}

export default function Dashboard(props) {
    const auth = usePage().props.auth.user;
    const { budgets, chartConfigBudget } = props.budgetChart;

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
                <div className="col-span-full space-y-6 lg:col-span-8">
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

                    {/* Bar Chart */}
                    <BarChartCustom
                        title="Pemasukan & Pengeluaran"
                        year={props.year}
                        chartData={props.incomeExpenseChart}
                    />
                </div>

                {/* Tabs & Pie Chart */}
                <div className="col-span-full space-y-6 lg:col-span-4">
                    {/* Tabs */}
                    <div className="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-card dark:border-0">
                        <Tabs defaultValue="goal" className="w-full">
                            <TabsList className="grid w-full grid-cols-3 rounded-xl bg-gray-100/50 p-1 dark:bg-gray-900/50">
                                <TabsTrigger
                                    value="goal"
                                    className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-violet-600 data-[state=active]:shadow-sm dark:data-[state=active]:bg-gray-800 dark:data-[state=active]:text-violet-400"
                                >
                                    Tujuan
                                </TabsTrigger>
                                <TabsTrigger
                                    value="income"
                                    className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-emerald-600 data-[state=active]:shadow-sm dark:data-[state=active]:bg-gray-800 dark:data-[state=active]:text-emerald-400"
                                >
                                    Pemasukan
                                </TabsTrigger>
                                <TabsTrigger
                                    value="expense"
                                    className="rounded-lg data-[state=active]:bg-white data-[state=active]:text-red-600 data-[state=active]:shadow-sm dark:data-[state=active]:bg-gray-800 dark:data-[state=active]:text-red-400"
                                >
                                    Pengeluaran
                                </TabsTrigger>
                            </TabsList>
                            <TabsContent value="goal" className="mt-6 space-y-6">
                                <div className="space-y-1">
                                    <h3 className="font-semibold text-gray-900 dark:text-gray-100">Tujuan Keuangan</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Langkah kecil untuk impian besar anda.
                                    </p>
                                </div>

                                <ExpandableList
                                    items={props.goals}
                                    className="space-y-5"
                                    emptyState={
                                        <EmptyState
                                            icon={IconMoneybag}
                                            title="Belum ada tujuan"
                                            subtitle="Buat tujuan finansial pertama anda sekarang."
                                        />
                                    }
                                    renderItem={(goal, index) => {
                                        const currentAmount = Number(goal.balances_sum_amount) + Number(goal.beginning_balance);
                                        return (
                                            <div key={index} className="group flex flex-col gap-3 rounded-xl border border-gray-100 bg-gray-50/50 p-4 transition-all hover:bg-white hover:shadow-md dark:border-gray-800 dark:bg-gray-800/50 dark:hover:bg-gray-800">
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center gap-3">
                                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                                                            <IconMoneybag size={20} />
                                                        </div>
                                                        <div>
                                                            <p className="font-semibold text-gray-900 dark:text-gray-100">{goal.name}</p>
                                                            <p className="text-xs text-gray-500 dark:text-gray-400">Target: {formatToRupiah(goal.nominal)}</p>
                                                        </div>
                                                    </div>
                                                    <div className="text-right">
                                                        <span className="text-sm font-bold text-violet-600 dark:text-violet-400">
                                                            {goal.percentage}%
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="space-y-1.5">
                                                    <div className="flex justify-between text-xs">
                                                        <span className="text-gray-500 dark:text-gray-400">Tercapai</span>
                                                        <span className="font-medium text-gray-900 dark:text-gray-100">{formatToRupiah(currentAmount)}</span>
                                                    </div>
                                                    <Progress value={goal.percentage} className="h-2 bg-gray-200 dark:bg-gray-700" indicatorClassName="bg-violet-500 dark:bg-violet-400" />
                                                </div>
                                            </div>
                                        );
                                    }}
                                />
                            </TabsContent>
                            <TabsContent value="income" className="mt-6 space-y-6">
                                <div className="space-y-1">
                                    <h3 className="font-semibold text-gray-900 dark:text-gray-100">Riwayat Pemasukan</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Daftar pemasukan terbaru anda.
                                    </p>
                                </div>
                                <ExpandableList
                                    items={props.incomes}
                                    className="space-y-4"
                                    emptyState={
                                        <EmptyState
                                            icon={IconDoorEnter}
                                            title="Belum ada data"
                                            subtitle="Data pemasukan akan muncul di sini."
                                        />
                                    }
                                    renderItem={(income, index) => (
                                        <div
                                            key={index}
                                            className="flex items-center justify-between rounded-xl border border-transparent p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                    <IconDoorEnter size={20} />
                                                </div>
                                                <div className="space-y-0.5">
                                                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{income.source.detail}</p>
                                                    <p className="text-xs text-gray-500 capitalize dark:text-gray-400">{income.source.type}</p>
                                                </div>
                                            </div>
                                            <p className="font-semibold text-emerald-600 dark:text-emerald-400">
                                                + {formatToRupiah(income.nominal)}
                                            </p>
                                        </div>
                                    )}
                                />
                            </TabsContent>
                            <TabsContent value="expense" className="mt-6 space-y-6">
                                <div className="space-y-1">
                                    <h3 className="font-semibold text-gray-900 dark:text-gray-100">Riwayat Pengeluaran</h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Pantau pengeluaran anda disini.
                                    </p>
                                </div>
                                <ExpandableList
                                    items={props.expenses}
                                    className="space-y-4"
                                    emptyState={
                                        <EmptyState
                                            icon={IconDoorExit}
                                            title="Belum ada data"
                                            subtitle="Data pengeluaran akan muncul di sini."
                                        />
                                    }
                                    renderItem={(expense, index) => (
                                        <div
                                            key={index}
                                            className="flex items-center justify-between rounded-xl border border-transparent p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                                    <IconDoorExit size={20} />
                                                </div>
                                                <div className="space-y-0.5">
                                                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{expense.description}</p>
                                                    <p className="text-xs text-gray-500 capitalize dark:text-gray-400">{expense.type}</p>
                                                </div>
                                            </div>
                                            <p className="font-semibold text-red-600 dark:text-red-400">
                                                - {formatToRupiah(expense.nominal)}
                                            </p>
                                        </div>
                                    )}
                                />
                            </TabsContent>
                        </Tabs>
                    </div>

                    {/* Pie Chart */}
                    <PieChartCustom
                        title="Anggaran"
                        year={props.year}
                        budgets={budgets}
                        chartConfig={chartConfigBudget}
                    />
                </div>
            </div>
        </div>
    );
}

Dashboard.layout = (page) => <AppLayout title="Dashboard" children={page} />;
