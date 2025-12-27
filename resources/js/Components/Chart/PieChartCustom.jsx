import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    ChartContainer,
    ChartLegend,
    ChartLegendContent,
    ChartTooltip,
    ChartTooltipContent,
} from '@/Components/ui/chart';
import { formatToRupiah } from '@/lib/utils';
import { useMemo } from 'react';
import { Label, Pie, PieChart } from 'recharts';

export default function PieChartCustom({ title, year, budgets, chartConfig }) {
    const totalNominals = useMemo(() => {
        return budgets.reduce((acc, curr) => acc + curr.nominals, 0);
    }, [budgets]);

    return (
        <Card className="flex flex-col">
            <CardHeader className="items-center pb-0">
                <CardTitle>{title}</CardTitle>
                <CardDescription>Periode Januari - Desember {year}</CardDescription>
            </CardHeader>
            <CardContent className="flex-1 pb-6">
                <ChartContainer config={chartConfig} className="mx-auto aspect-square max-h-[300px]">
                    <PieChart>
                        <ChartTooltip
                            cursor={false}
                            content={
                                <ChartTooltipContent
                                    hideLabel
                                    formatter={(value, name, item, index) => (
                                        <>
                                            <div
                                                className="h-2.5 w-2.5 shrink-0 rounded-[2px] bg-[--color-bg]"
                                                style={{
                                                    '--color-bg': `var(--color - ${name})`,
                                                }}
                                            />
                                            <div
                                                className="flex min-w-[130px] items-center gap-2 text-xs
                                                    text-muted-foreground"
                                            >
                                                {chartConfig[name]?.label || name}
                                                <div
                                                    className="ml-auto flex items-baseline gap-0.5 font-mono font-medium
                                                        tabular-nums text-foreground"
                                                >
                                                    {formatToRupiah(value)}
                                                </div>
                                            </div>
                                        </>
                                    )}
                                />
                            }
                        />
                        <Pie data={budgets} dataKey="nominals" nameKey="type" innerRadius={70} strokeWidth={5}>
                            <Label
                                content={({ viewBox }) => {
                                    if (viewBox && 'cx' in viewBox && 'cy' in viewBox) {
                                        return (
                                            <text
                                                x={viewBox.cx}
                                                y={viewBox.cy}
                                                textAnchor="middle"
                                                dominantBaseline="middle"
                                            >
                                                <tspan
                                                    x={viewBox.cx}
                                                    y={viewBox.cy}
                                                    className="fill-foreground text-xl font-bold"
                                                >
                                                    {totalNominals.toLocaleString('id-ID')}
                                                </tspan>
                                                <tspan
                                                    x={viewBox.cx}
                                                    y={(viewBox.cy || 0) + 24}
                                                    className="fill-muted-foreground"
                                                >
                                                    Total Anggaran
                                                </tspan>
                                            </text>
                                        );
                                    }
                                }}
                            />
                        </Pie>
                    </PieChart>
                </ChartContainer>
                <div className="mt-4 flex flex-col gap-3">
                    {budgets.map((item, index) => {
                        const percentage = ((item.nominals / totalNominals) * 100).toFixed(1);
                        return (
                            <div key={index} className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <div className="h-3 w-3 rounded-full" style={{ backgroundColor: item.fill }} />
                                    <span className="text-sm text-muted-foreground">
                                        {chartConfig[item.type]?.label || item.type}
                                    </span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <span className="text-sm text-muted-foreground">({percentage}%)</span>
                                    <span className="text-sm font-medium">{formatToRupiah(item.nominals)}</span>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </CardContent>
        </Card>
    );
}
