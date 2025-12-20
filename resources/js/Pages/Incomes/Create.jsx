import BreadcrumbHeader from '@/Components/BreadcrumbHeader';
import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Textarea } from '@headlessui/react';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowBack, IconCheck, IconDoorEnter } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Create(props) {
    // destruct
    const { data, setData, errors, post, processing, reset } = useForm({
        source_id: null,
        date: '',
        nominal: '',
        notes: '',
        month: null,
        year: null,
        method: props.pageSettings.method,
    });

    const onHandleChange = (e) => setData(e.target.name, e.target.value);
    const onHandleSubmit = (e) => {
        e.preventDefault();
        if (processing) return; // Prevent double submit
        post(props.pageSettings.action, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (success) => {
                const flash = flashMessage(success);
                if (flash) toast[flash.type](flash.message);
            },
        });
    };

    return (
        <div className="flex w-full flex-col gap-y-6 pb-32">
            <BreadcrumbHeader items={props.items} />

            <Card>
                <CardHeader>
                    <div className="flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                        <HeaderTitle
                            title={props.pageSettings.title}
                            subtitle={props.pageSettings.subtitle}
                            icon={IconDoorEnter}
                        />

                        <Button variant="emerald" size="xl" asChild>
                            <Link href={route('incomes.index')}>
                                <IconArrowBack size="4" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <form className="space-y-4" onSubmit={onHandleSubmit}>
                        <div className="flex flex-col gap-2">
                            <Label htmlFor="source_id">Sumber</Label>
                            <Select value={data.source_id} onValueChange={(value) => setData('source_id', value)}>
                                <SelectTrigger>
                                    <SelectValue>
                                        {props.sources.find((source) => source.value == data.source_id)?.label ??
                                            'Pilih Sumber'}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    {props.sources.map((source, index) => (
                                        <SelectItem key={index} value={source.value}>
                                            {source.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.source_id && <InputError message={errors.source_id} />}
                        </div>

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
                            <Label htmlFor="notes">Catatan</Label>
                            <Textarea
                                name="notes"
                                id="notes"
                                placeholder="Masukkan Catatan"
                                value={data.notes}
                                onChange={onHandleChange}
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            {errors.notes && <InputError message={errors.notes} />}
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

Create.layout = (page) => <AppLayout title={page.props.pageSettings.title} children={page} />;
