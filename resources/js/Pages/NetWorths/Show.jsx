import BreadcrumbHeader from "@/Components/BreadcrumbHeader";
import HeaderTitle from "@/Components/HeaderTitle";
import { Button } from "@/Components/ui/button";
import { Card, CardHeader } from "@/Components/ui/card";
import AppLayout from "@/Layouts/AppLayout";
import { Link } from "@inertiajs/react";
import { IconArrowBack, IconMenorah } from "@tabler/icons-react";

export default function Show(props) {
    return (
        <div className="flex flex-col w-full pb-32 gap-y-6">
            <BreadcrumbHeader items={props.items} />
            <Card>
                <CardHeader className="p-0">
                    <div className="flex flex-col items-start justify-between
                    p-4 gap-y-4 lg:flex-row lg:items-center">
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

        </div>
    );
}

Show.layout = (page) => <AppLayout children={page} title={page.props.pageSettings.title} />