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
import { Link, useForm } from '@inertiajs/react';
import { IconArrowBack, IconCheck, IconCreditCardPay, IconMenorah } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Edit(props) {
    // destruct
    const { data, setData, errors, put, processing, reset } = useForm({
        net_worth_goal: props.netWorth.net_worth_goal ?? 0,
        transaction_per_month: props.netWorth.transaction_per_month ?? 1,
        method: props.pageSettings.method,
    });

    const onHandleChange = (e) => setData(e.target.name, e.target.value);
    const onHandleSubmit = (e) => {
        e.preventDefault();
        put(props.pageSettings.action, {
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
                            icon={IconMenorah}
                        />

                        <Button variant="emerald" size="xl" asChild>
                            <Link href={route('net-worths.index')}>
                                <IconArrowBack size="4" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <form className="space-y-4" onSubmit={onHandleSubmit}>
                        <div className="flex flex-col gap-2">
                            <Label htmlFor="net_worth_goal">Tujuan Kekayaan Bersih</Label>
                            <Input
                                type="text"
                                name="net_worth_goal"
                                id="net_worth_goal"
                                placeholder="Masukkan tujuan kekayaan bersih"
                                value={data.net_worth_goal}
                                onChange={onHandleChange}
                            />
                            {errors.net_worth_goal && <InputError message={errors.net_worth_goal} />}
                        </div>

                        <div className="flex flex-col gap-2">
                            <Label htmlFor="transaction_per_month">Transaksi per Bulan</Label>
                            <Input
                                type="number"
                                name="transaction_per_month"
                                id="transaction_per_month"
                                placeholder="Masukkan transaksi per bulan"
                                value={data.transaction_per_month}
                                onChange={onHandleChange}
                            />
                            {errors.transaction_per_month && <InputError message={errors.transaction_per_month} />}
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
