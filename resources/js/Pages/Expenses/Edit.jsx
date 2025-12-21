import BreadcrumbHeader from '@/Components/BreadcrumbHeader';
import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { UseFilter } from '@/Hooks/UseFilter';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Textarea } from '@headlessui/react';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowBack, IconCheck, IconDoorExit } from '@tabler/icons-react';
import { useEffect, useState } from 'react';
import { toast } from 'sonner';

export default function Edit(props) {
    const [params, setParams] = useState({ ...props.state, type: props.expense.type ?? props.state.type });
    UseFilter({
        route: route('expenses.edit', props.expense),
        values: params,
        only: ['budgets'],
    });

    // destruct
    const { data, setData, errors, put, processing, reset } = useForm({
        date: props.expense.date ?? '',
        description: props.expense.description ?? '',
        nominal: props.expense.nominal ?? '',
        type: props.expense.type ?? params.type,
        type_detail_id: props.expense.type_detail_id ?? null,
        notes: props.expense.notes ?? '',
        payment_id: props.expense.payment_id ?? null,
        month: props.expense.month ?? null,
        year: props.expense.year ?? null,
        method: props.pageSettings.method,
    });

    const onHandleChange = (e) => setData(e.target.name, e.target.value);
    const onHandleSubmit = (e) => {
        e.preventDefault();
        if (processing) return; // Prevent double submit
        put(props.pageSettings.action, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (success) => {
                const flash = flashMessage(success);
                if (flash) toast[flash.type](flash.message);
            },
        });
    };

    useEffect(() => {
        setData('type', params.type);
    }, [params.type]);

    return (
        <div className="flex w-full flex-col gap-y-6 pb-32">
            <BreadcrumbHeader items={props.items} />
            {/* alert */}
            {props.budgets.length === 0 && params?.type && (
                <Alert variant="destructive">
                    <AlertDescription>
                        Tipe Detail ditemukan pada tipe <strong>{params?.type}</strong>. Silahkan untuk membuat terlebih
                        dahulu
                    </AlertDescription>
                </Alert>
            )}

            <Card>
                <CardHeader>
                    <div className="flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                        <HeaderTitle
                            title={props.pageSettings.title}
                            subtitle={props.pageSettings.subtitle}
                            icon={IconDoorExit}
                        />

                        <Button variant="emerald" size="xl" asChild>
                            <Link href={route('expenses.index')}>
                                <IconArrowBack size="4" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <form className="space-y-4" onSubmit={onHandleSubmit}>
                        <div className="flex flex-col gap-2">
                            <Label htmlFor="date">Tanggal</Label>
                            <Input
                                type="date"
                                name="date"
                                id="date"
                                placeholder="Masukkan Tanggal"
                                value={data.date}
                                onChange={onHandleChange}
                            />
                            {errors.date && <InputError message={errors.date} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="description">Deskripsi</Label>
                            <Textarea
                                name="description"
                                id="description"
                                placeholder="Masukkan Deskripsi"
                                value={data.description}
                                onChange={onHandleChange}
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3
                                    py-2 text-sm ring-offset-background placeholder:text-muted-foreground
                                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring
                                    focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            {errors.description && <InputError message={errors.description} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="nominal">Nominal</Label>
                            <Input
                                type="number"
                                name="nominal"
                                id="nominal"
                                placeholder="Masukkan Nominal"
                                value={data.nominal}
                                onChange={onHandleChange}
                            />
                            {errors.notes && <InputError message={errors.notes} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="type">Tipe</Label>
                            <Select
                                defaultValue={data.type}
                                onValueChange={(value) => setParams({ ...params, type: value })}
                            >
                                <SelectTrigger>
                                    <SelectValue>
                                        {props.types.find((type) => type.value == data.type)?.label ?? 'Pilih Tipe'}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    {props.types.map((type, index) => (
                                        <SelectItem key={index} value={type.value}>
                                            {type.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.type && <InputError message={errors.type} />}
                        </div>

                        {props.budgets.length > 0 && (
                            <>
                                <div className="flex flex-col gap-2">
                                    <Label htmlFor="type_detail_id">Detail</Label>
                                    <Select
                                        defaultValue={data.type_detail_id}
                                        onValueChange={(value) => setData('type_detail_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue>
                                                {props.budgets.find((budget) => budget.value == data.type_detail_id)
                                                    ?.label ?? 'Pilih Detail'}
                                            </SelectValue>
                                        </SelectTrigger>
                                        <SelectContent>
                                            {props.budgets.map((budget, index) => (
                                                <SelectItem key={index} value={budget.value}>
                                                    {budget.label} - ({budget.month}/{budget.year})
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.type_detail_id && <InputError message={errors.type_detail_id} />}
                                </div>
                            </>
                        )}

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="payment_id">Metode Pembayaran</Label>
                            <Select
                                defaultValue={data.payment_id}
                                onValueChange={(value) => setData('payment_id', value)}
                            >
                                <SelectTrigger>
                                    <SelectValue>
                                        {props.payments.find((payment) => payment.value == data.payment_id)?.label ??
                                            'Pilih Metode Pembayaran'}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    {props.payments.map((payment, index) => (
                                        <SelectItem key={index} value={payment.value}>
                                            {payment.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.payment_id && <InputError message={errors.payment_id} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="notes">Catatan</Label>
                            <Textarea
                                name="notes"
                                id="notes"
                                placeholder="Masukkan Catatan"
                                value={data.notes}
                                onChange={onHandleChange}
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3
                                    py-2 text-sm ring-offset-background placeholder:text-muted-foreground
                                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring
                                    focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            {errors.description && <InputError message={errors.description} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="month">Bulan</Label>
                            <Select defaultValue={data.month} onValueChange={(value) => setData('month', value)}>
                                <SelectTrigger>
                                    <SelectValue>
                                        {props.months.find((month) => month.value == data.month)?.label ??
                                            'Pilih Bulan'}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    {props.months.map((month, index) => (
                                        <SelectItem key={index} value={month.value}>
                                            {month.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.month && <InputError message={errors.month} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="year">Tahun</Label>
                            <Select defaultValue={data.year} onValueChange={(value) => setData('year', value)}>
                                <SelectTrigger>
                                    <SelectValue>
                                        {props.years.find((year) => year == data.year) ?? 'Pilih Tahun'}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    {props.years.map((year, index) => (
                                        <SelectItem key={index} value={year}>
                                            {year}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.year && <InputError message={errors.year} />}
                        </div>

                        <div className="mt-8 flex flex-col gap-2 lg:flex-row lg:justify-end">
                            <Button type="button" variant="ghost" size="xl" onClick={() => reset()}>
                                Reset
                            </Button>
                            <Button type="submit" variant="emerald" size="xl" disabled={processing}>
                                <IconCheck />
                                Submit
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}

Edit.layout = (page) => <AppLayout title={page.props.pageSettings.title} children={page} />;
