import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { ChartData, ChartOptions } from 'chart.js';
import { AvatarModule } from 'primeng/avatar';
import { ButtonModule } from 'primeng/button';
import { ChartModule } from 'primeng/chart';
import { ProgressBarModule } from 'primeng/progressbar';
import { TagModule } from 'primeng/tag';

type RangeKey = '30d' | 'all';
type ToneKey = 'green' | 'blue' | 'orange' | 'purple' | 'gray';
type BadgeSeverity = 'success' | 'info' | 'warning' | 'help' | 'secondary';

interface SummaryCard {
    label: string;
    value: string;
    icon: string;
    tone: ToneKey;
}

interface RangeOption {
    label: string;
    value: RangeKey;
}

interface TopicCard {
    title: string;
    subtitle: string;
    badge: string;
    badgeSeverity: BadgeSeverity;
    icon: string;
    tone: ToneKey;
    progress?: number;
    note?: string;
    featured?: boolean;
    disabled?: boolean;
}

@Component({
    selector: 'app-history',
    standalone: true,
    imports: [CommonModule, RouterModule, ButtonModule, ChartModule, AvatarModule, ProgressBarModule, TagModule],
    templateUrl: './history.component.html',
    styleUrls: ['./history.component.css']
})
export class HistoryComponent {
    readonly summaryCards: SummaryCard[] = [
        {
            label: 'متوسط الأداء',
            value: '78%',
            icon: 'pi pi-chart-line',
            tone: 'green'
        },
        {
            label: 'التدريبات المنجزة',
            value: '12',
            icon: 'pi pi-graduation-cap',
            tone: 'blue'
        },
        {
            label: 'المستوى الحالي',
            value: '3',
            icon: 'pi pi-sparkles',
            tone: 'purple'
        }
    ];

    readonly rangeOptions: RangeOption[] = [
        { label: 'آخر 30 يوم', value: '30d' },
        { label: 'الكل', value: 'all' }
    ];

    readonly topicCards: TopicCard[] = [
        {
            title: 'تدريب القراءة المكثف',
            subtitle: 'المستوى 2 • 9 أسابيع',
            badge: '94%',
            badgeSeverity: 'success',
            icon: 'pi pi-star-fill',
            tone: 'green',
            progress: 94,
            note: 'تحسن واضح، استمروا على نفس الوتيرة',
            featured: true
        },
        {
            title: 'تحدي الحساب الذهني',
            subtitle: 'المستوى 5 • 15 تكرار',
            badge: '82%',
            badgeSeverity: 'info',
            icon: 'pi pi-calculator',
            tone: 'blue'
        },
        {
            title: 'اختبار الذاكرة البصرية',
            subtitle: 'التقييم الأول • 10 تكرار',
            badge: '65%',
            badgeSeverity: 'warning',
            icon: 'pi pi-lightbulb',
            tone: 'orange'
        },
        {
            title: 'تمرين النطق',
            subtitle: 'المستوى 1 • 8 تكرار',
            badge: '78%',
            badgeSeverity: 'help',
            icon: 'pi pi-users',
            tone: 'purple'
        },
        {
            title: 'التقييم الشامل',
            subtitle: 'بعد 1 جلسة • 1 أكتوبر',
            badge: '-',
            badgeSeverity: 'secondary',
            icon: 'pi pi-flag',
            tone: 'gray',
            disabled: true
        }
    ];

    selectedRange: RangeKey = '30d';

    chartData: ChartData<'line'> = this.buildChartData(this.selectedRange);

    chartOptions: ChartOptions<'line'> = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                border: {
                    display: false
                },
                ticks: {
                    color: '#94a3b8',
                    font: {
                        size: 12,
                        family: 'inherit'
                    }
                }
            },
            y: {
                min: 0,
                max: 100,
                ticks: {
                    display: false
                },
                grid: {
                    color: 'rgba(148, 163, 184, 0.18)'
                },
                border: {
                    display: false
                }
            }
        },
        elements: {
            line: {
                tension: 0.38
            },
            point: {
                radius: 5,
                hoverRadius: 6,
                hitRadius: 18
            }
        }
    };

    setRange(range: RangeKey): void {
        if (this.selectedRange === range) {
            return;
        }

        this.selectedRange = range;
        this.chartData = this.buildChartData(range);
    }

    getRangeButtonClass(value: RangeKey): string {
        return value === this.selectedRange ? 'range-pill range-pill--active' : 'range-pill range-pill--inactive';
    }

    trackByLabel(_: number, item: SummaryCard): string {
        return item.label;
    }

    trackByRange(_: number, item: RangeOption): RangeKey {
        return item.value;
    }

    trackByTopic(_: number, item: TopicCard): string {
        return item.title;
    }

    private buildChartData(range: RangeKey): ChartData<'line'> {
        const labels = ['اليوم', '15 أكتوبر', '10 أكتوبر', '5 أكتوبر', '1 أكتوبر'];
        const points = range === '30d' ? [14, 34, 29, 61, 84] : [10, 26, 23, 49, 71];

        return {
            labels,
            datasets: [
                {
                    label: 'نتائج التدريبات',
                    data: points,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.12)',
                    borderWidth: 4,
                    fill: true,
                    pointBackgroundColor: '#111827',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointHoverBackgroundColor: '#111827',
                    pointHoverBorderColor: '#ffffff'
                }
            ]
        };
    }
}
