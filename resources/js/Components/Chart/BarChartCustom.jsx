import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    ChartContainer,
    ChartLegend,
    ChartLegendContent,
    ChartTooltip,
    ChartTooltipContent,
} from '@/Components/ui/chart';
import { Bar, BarChart, XAxis } from 'recharts';

export default function BarChartCustom({ title, year, chartData }) {
    const chartConfig = {
        pemasukan: {
            label: 'Pemasukan',
            color: 'hsl(var(--chart-1))',
        },
        pengeluaran: {
            label: 'Pengeluaran',
            color: 'hsl(var(--chart-2))',
        },
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>{title}</CardTitle>
                <CardDescription>Periode Januari - Desember {year}</CardDescription>
            </CardHeader>
            <CardContent>
                <ChartContainer config={chartConfig}>
                    <BarChart accessibilityLayer data={chartData}>
                        <XAxis
                            dataKey="month"
                            tickLine={false}
                            tickMargin={10}
                            axisLine={false}
                            tickFormatter={(value) => value.slice(0, 3)}
                        />
                        <ChartTooltip cursor={false} content={<ChartTooltipContent indicator="dashed" />} />
                        <ChartLegend content={<ChartLegendContent />} />
                        <Bar
                            dataKey="pemasukan"
                            fill="var(--color-pemasukan)"
                            fillOpacity={0.9}
                            radius={[8, 8, 0, 0]}
                        />
                        <Bar
                            dataKey="pengeluaran"
                            fill="var(--color-pengeluaran)"
                            fillOpacity={0.9}
                            radius={[8, 8, 0, 0]}
                        />
                    </BarChart>
                </ChartContainer>
            </CardContent>
        </Card>
    );
}
