import AlertAction from '@/Components/AlertAction';
import BreadcrumbHeader from '@/Components/BreadcrumbHeader';
import CardStat from '@/Components/CardStat';
import Filter from '@/Components/Datatable/Filter';
import PaginationTable from '@/Components/Datatable/PaginationTable';
import ShowFilter from '@/Components/Datatable/ShowFilter';
import EmptyState from '@/Components/EmptyState';
import HeaderTitle from '@/Components/HeaderTitle';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableBody, TableCell, TableFooter, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { UseFilter } from '@/Hooks/UseFilter';
import AppLayout from '@/Layouts/AppLayout';
import { BUDGETTYPEVARIANT, deleteAction, formatDateIndo, formatToRupiah } from '@/lib/utils';
import {
    IconCash,
    IconDelta,
    IconDoorEnter,
    IconDoorExit,
    IconInvoice,
    IconLogs,
    IconMoneybag,
    IconShoppingBag,
} from '@tabler/icons-react';
import { useState } from 'react';

export default function Index(props) {
    const budgetIncomes = props.reports.budgetIncomes.data;
    const budgetSavings = props.reports.budgetSavings.data;
    const budgetDebts = props.reports.budgetDebts.data;
    const budgetBills = props.reports.budgetBills.data;
    const budgetShoppings = props.reports.budgetShoppings.data;

    const incomeTrackers = props.incomeTrackers;
    const expenseTrackers = props.expenseTrackers;

    const [params, setParams] = useState(props.state);

    UseFilter({
        route: route('report-trackings'),
        values: params,
        only: ['reports', 'incomeTrackers', 'expenseTrackers'],
    });

    return (
        <div className="flex w-full flex-col gap-y-6 pb-32">
            <BreadcrumbHeader items={props.items} />

            <Card>
                <CardHeader className="p-0">
                    <div className="flex flex-col items-start justify-between gap-y-4 p-4 lg:flex-row lg:items-center">
                        <HeaderTitle
                            title={props.pageSettings.title}
                            subtitle={props.pageSettings.subtitle}
                            icon={IconLogs}
                        />
                        {/* filter */}
                        <div className="flex flex-row gap-x-4">
                            {/* Months */}
                            <Select value={params?.month} onValueChange={(e) => setParams({ ...params, month: e })}>
                                <SelectTrigger className="w-full sm:w-24">
                                    <SelectValue placeholder="Bulan" />
                                </SelectTrigger>
                                <SelectContent>
                                    {props.months.map((month, index) => (
                                        <SelectItem key={index} value={month.value}>
                                            {month.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            {/* Years */}
                            <Select
                                value={params?.year?.toString()}
                                onValueChange={(e) => setParams({ ...params, year: e })}
                            >
                                <SelectTrigger className="w-full sm:w-24">
                                    <SelectValue placeholder="Tahun" />
                                </SelectTrigger>
                                <SelectContent>
                                    {props.years.map((year, index) => (
                                        <SelectItem key={index} value={year?.toString()}>
                                            {year}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-5">
                <CardStat
                    data={{
                        title: 'Penghasilan',
                        icon: IconCash,
                        background: 'text-white bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.reports.budgetIncomes.total.actual)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Tabungan dan Investasi',
                        icon: IconMoneybag,
                        background: 'text-white bg-gradient-to-r from-orange-400 via-orange-500 to-orange-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.reports.budgetSavings.total.actual)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Cicilan Hutang',
                        icon: IconDelta,
                        background: 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.reports.budgetDebts.total.actual)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Tagihan',
                        icon: IconInvoice,
                        background: 'text-white bg-gradient-to-r from-sky-400 via-sky-500 to-sky-500',
                    }}
                >
                    <div className="text-2xl font-bold">{formatToRupiah(props.reports.budgetBills.total.actual)}</div>
                </CardStat>
                <CardStat
                    data={{
                        title: 'Belanja',
                        icon: IconShoppingBag,
                        background: 'text-white bg-gradient-to-r from-purple-400 via-purple-500 to-purple-500',
                    }}
                >
                    <div className="text-2xl font-bold">
                        {formatToRupiah(props.reports.budgetShoppings.total.actual)}
                    </div>
                </CardStat>
            </div>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div className="col-span-full">
                    <Card>
                        <CardHeader>
                            <CardTitle>Penghasilan</CardTitle>
                            <CardDescription>
                                Menampilkan sumber dan total pendapatan anda, baik dari gaji, bisnis, maupun pendapatan
                                pasif lainnya.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="p-0 [&-td]:whitespace-nowrap [&-td]:px-6 [&-th]:px-6">
                            {budgetIncomes.length === 0 ? (
                                <EmptyState
                                    icon={IconCash}
                                    title="Tidak ada penghasilan"
                                    subtitle="Mulailah dengan membuat penghasilan baru"
                                />
                            ) : (
                                <Table className="w-full">
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Sumber</TableHead>
                                            <TableHead>Rencana</TableHead>
                                            <TableHead>Sebenarnya</TableHead>
                                            <TableHead>Perbedaan</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {budgetIncomes.map((income, index) => (
                                            <TableRow key={index}>
                                                <TableCell>{income.detail}</TableCell>
                                                <TableCell>{formatToRupiah(income.plan)}</TableCell>
                                                <TableCell>{formatToRupiah(income.actual)}</TableCell>
                                                <TableCell>{formatToRupiah(income.difference)}</TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                    <TableFooter className="bg-emerald-500 font-bold text-white">
                                        <TableRow>
                                            <TableCell>Total</TableCell>
                                            <TableCell>
                                                {formatToRupiah(props.reports.budgetIncomes.total.plan)}
                                            </TableCell>
                                            <TableCell>
                                                {formatToRupiah(props.reports.budgetIncomes.total.actual)}
                                            </TableCell>
                                            <TableCell>
                                                {formatToRupiah(props.reports.budgetIncomes.total.difference)}
                                            </TableCell>
                                        </TableRow>
                                    </TableFooter>
                                </Table>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* LACAK PEMASUKAN */}
            <Card>
                <CardHeader>
                    <CardTitle>Lacak Pemasukan</CardTitle>
                    <CardDescription>
                        Memberikan detail setiap transaksi pemasukan untuk mengetahui sumber pendapatan secara rinci.
                    </CardDescription>
                </CardHeader>
                <CardContent className="p-0 [&-td]:whitespace-nowrap [&-td]:px-6 [&-th]:px-6">
                    {incomeTrackers.length === 0 ? (
                        <EmptyState
                            icon={IconDoorEnter}
                            title="Tidak ada pemasukan"
                            subtitle="Mulailah dengan membuat pemasukan baru"
                        />
                    ) : (
                        <Table className="w-full">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>#</TableHead>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>Sumber</TableHead>
                                    <TableHead>Nominal</TableHead>
                                    <TableHead>Catatan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {incomeTrackers.map((incomeTracker, index) => (
                                    <TableRow key={index}>
                                        <TableCell>{index + 1}</TableCell>
                                        <TableCell>{formatDateIndo(incomeTracker.date)}</TableCell>
                                        <TableCell>{incomeTracker.source.detail}</TableCell>
                                        <TableCell>{formatToRupiah(incomeTracker.nominal)}</TableCell>
                                        <TableCell>{incomeTracker.notesTabuy}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </CardContent>
            </Card>

            {/* LACAK PENGELUARAN */}
            <Card>
                <CardHeader>
                    <CardTitle>Lacak Pengeluaran</CardTitle>
                    <CardDescription>
                        Memantau semua transaksi pengeluaran anda untuk membantu mengelola anggaran secara efisien.
                    </CardDescription>
                </CardHeader>
                <CardContent className="p-0 [&-td]:whitespace-nowrap [&-td]:px-6 [&-th]:px-6">
                    {expenseTrackers.length === 0 ? (
                        <EmptyState
                            icon={IconDoorExit}
                            title="Tidak ada pengeluaran"
                            subtitle="Mulailah dengan membuat pengeluaran baru"
                        />
                    ) : (
                        <Table className="w-full">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>#</TableHead>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>Deskripsi</TableHead>
                                    <TableHead>Nominal</TableHead>
                                    <TableHead>Tipe</TableHead>
                                    <TableHead>Detail</TableHead>
                                    <TableHead>Metode Pembayaran</TableHead>
                                    <TableHead>Catatan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {expenseTrackers.map((expenseTracker, index) => (
                                    <TableRow key={index}>
                                        <TableCell>{index + 1}</TableCell>
                                        <TableCell>{formatDateIndo(expenseTracker.date)}</TableCell>
                                        <TableCell>{expenseTracker.description}</TableCell>
                                        <TableCell>{formatToRupiah(expenseTracker.nominal)}</TableCell>
                                        <TableCell>
                                            <Badge variant={BUDGETTYPEVARIANT[expenseTracker.type]}>
                                                {expenseTracker.type}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{expenseTracker.typeDetail.detail}</TableCell>
                                        <TableCell>{expenseTracker.payment.name}</TableCell>
                                        <TableCell>{expenseTracker.notes}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </CardContent>
            </Card>
        </div>
    );
}

Index.layout = (page) => <AppLayout title={page.props.pageSettings?.title} children={page} />;
