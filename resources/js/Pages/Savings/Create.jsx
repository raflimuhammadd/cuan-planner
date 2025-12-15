import BreadcrumbHeader from "@/Components/BreadcrumbHeader";
import HeaderTitle from "@/Components/HeaderTitle";
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardHeader } from "@/Components/ui/card";
import AppLayout from "@/Layouts/AppLayout";
import { Link, useForm } from "@inertiajs/react";
import { IconArrowBack, IconMoneybag } from "@tabler/icons-react";

export default function Create(props) {

    // destruct
    const { } = useForm({
       'name': '',
       'nominal': 0,
       'monthly_saving': 0,
       'deadline': '',
       'beginning_balance': 0,
       'method': props.pageSettings.method 
    });

    return (
        <div className="flex w-full flex-col gap-y-6 pb-32">
            <BreadcrumbHeader items={props.items}/>

            <Card>
                <CardHeader>
                    <div className="flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                        <HeaderTitle
                            title={props.pageSettings.title}
                            subtitle={props.pageSettings.subtitle}
                            icon={IconMoneybag}
                        />

                        <Button variant="emerald" size="xl" asChild>
                            <Link href={route('goals.index')}>
                                <IconArrowBack size="4" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent></CardContent>
            </Card>

        </div>
    )
}

Create.layout = (page) => <AppLayout title={page.props.pageSettings.title}  children={page}/>