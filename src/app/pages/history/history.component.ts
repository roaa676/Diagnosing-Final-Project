import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { Subject, takeUntil } from 'rxjs';
import { HistoryService, HistoryEntry } from '@/core/services/history.service';
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
export class HistoryComponent implements OnInit, OnDestroy {
    constructor(private readonly historyService: HistoryService) { }
    entries: HistoryEntry[] = [];
    loading = false;
    error = '';
    private privateTimeseries: any[] = [];
    private destroy$ = new Subject<void>();

    summaryCards: SummaryCard[] = [
        { label: 'متوسط الأداء', value: '-', icon: 'pi pi-chart-line', tone: 'green' },
        { label: 'التدريبات المنجزة', value: '-', icon: 'pi pi-graduation-cap', tone: 'blue' },
        { label: 'المستوى الحالي', value: '-', icon: 'pi pi-sparkles', tone: 'purple' }
    ];

    readonly rangeOptions: RangeOption[] = [
        { label: 'آخر 30 يوم', value: '30d' },
        { label: 'الكل', value: 'all' }
    ];

    topicCards: TopicCard[] = [];
    private reportData: any = null;

    selectedRange: RangeKey = '30d';

    chartData!: ChartData<'line'>;

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

    formatEntryDate(timestamp: string): string {
        if (!timestamp) {
            return '-';
        }

        return new Date(timestamp).toLocaleString('ar-EG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    getEntryScoreLabel(entry: HistoryEntry): string {
        const result = entry.result;
        if (!result) {
            return '-';
        }

        if (entry.activity_type === 'استبيان ولي الأمر') {
            return `الدرجة: ${result.score ?? '-'} (${result.risk_level ?? '-'})`;
        }

        const percent = result.score;
        const parts = [
            percent !== undefined && percent !== null ? `${percent}%` : null,
            result.raw_score !== undefined ? `نقاط: ${result.raw_score}` : null,
            result.correct_count !== undefined && result.total_questions
                ? `صح: ${result.correct_count}/${result.total_questions}`
                : null,
            result.level ? `المستوى ${result.level}` : null,
        ].filter(Boolean);

        return parts.length ? parts.join(' • ') : '-';
    }


    ngOnInit(): void {
        console.log('History Init');
        this.chartData = this.buildChartData(this.selectedRange);
        const childId = this.getLocalStorageNumber('selected_child_id');
        console.log('selected_child_id = ', childId);
        if (!childId) {
            this.error = 'لم يتم تحديد الطفل لعرض السجل';
            return;
        }

        this.loading = true;
        this.historyService
            .getChildReport(childId)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res: any) => {

                    console.log('REPORT RESPONSE', res);

                    this.reportData = res;

                    console.log('REPORT DATA', this.reportData);

                    this.applyReportToUI();

                    console.log('SUMMARY', this.summaryCards);
                    console.log('TOPICS', this.topicCards);

                    this.loading = false;
                },
                error: (err) => {
                    console.log('REPORT ERROR', err);
                    this.error = 'فشل تحميل تقرير الأداء';
                    this.loading = false;
                }
            });

        this.historyService
            .getChildHistory(childId)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res: any) => {
                    this.entries = res?.data ?? [];
                },
                error: () => {
                    // non-fatal: show message in list
                }
            });
    }

    private applyReportToUI(): void {
        if (!this.reportData) {
            return;
        }

        const avg =
            this.reportData.average_score ??
            this.reportData.avg_score ??
            null;

        const completed =
            this.reportData.completed_trainings_count ??
            this.reportData.completed_count ??
            null;

        const currentLevel =
            this.reportData.current_level ??
            this.reportData.level ??
            null;

        this.summaryCards = [
            {
                label: 'المستوى الحالي',
                value: String(currentLevel ?? '-'),
                icon: 'pi pi-sparkles',
                tone: 'purple'
            },
            {
                label: 'التدريبات المنجزة',
                value: String(completed ?? '-'),
                icon: 'pi pi-graduation-cap',
                tone: 'blue'
            },
            {
                label: 'متوسط الأداء',
                value: avg !== null ? `${Math.round(avg)}%` : '-',
                icon: 'pi pi-chart-line',
                tone: 'green'
            }
        ];

        const assessmentCard: TopicCard[] =
            this.reportData.assessments?.parent_questionnaire
                ? [
                    {
                        title: 'التقييم المبدئي',
                        subtitle:
                            this.reportData.assessments.parent_questionnaire.date ?? '',
                        badge:
                            this.reportData.assessments.parent_questionnaire.risk_level ??
                            '-',
                        badgeSeverity: 'warning',
                        icon: 'pi pi-flag-fill',
                        tone: 'orange',
                        progress: 100,
                        featured: true
                    }
                ]
                : [];

        const trainingCards: TopicCard[] =
            (this.reportData.trainings || this.reportData.topics || []).map(
                (t: any) => ({
                    title: t.title ?? t.name ?? 'تمرين',
                    subtitle:
                        t.subtitle ??
                        `المستوى ${t.level ?? '-'} ${t.repetitions ? '• ' + t.repetitions : ''}`,
                    badge:
                        t.badge ??
                        (t.score !== undefined
                            ? `${Math.round(t.score)}%`
                            : '-'),
                    badgeSeverity:
                        t.score >= 90
                            ? 'success'
                            : t.score >= 70
                                ? 'info'
                                : 'warning',
                    icon: t.icon ?? 'pi pi-star-fill',
                    tone: t.tone ?? 'green',
                    progress: t.progress ?? t.score ?? 0,
                    note: t.note,
                    featured: true,
                    disabled: !!t.disabled
                })
            );

        this.topicCards = [
            ...assessmentCard,
            ...trainingCards
        ];

        const timeseries =
            this.reportData.timeseries ??
            this.reportData.history_chart ??
            null;

        if (Array.isArray(timeseries) && timeseries.length) {
            this.privateTimeseries = timeseries;
            this.chartData = this.buildChartData(this.selectedRange);
        }
    }


    ngOnDestroy(): void {
        this.destroy$.next();
        this.destroy$.complete();
    }

    private getLocalStorageNumber(key: string): number | null {
        try {
            const rawValue = localStorage.getItem(key);
            const value = Number(rawValue);
            return Number.isFinite(value) && value > 0 ? value : null;
        } catch {
            return null;
        }
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
        const source = this.privateTimeseries.length ? this.privateTimeseries : [];

        const filtered = range === '30d'
            ? source.filter((point: any) => {
                const dateValue = point.date ?? point.label;
                if (!dateValue) {
                    return true;
                }
                const pointDate = new Date(dateValue);
                const cutoff = new Date();
                cutoff.setDate(cutoff.getDate() - 30);
                return pointDate >= cutoff;
            })
            : source;

        if (filtered.length) {
            const labels = filtered.map(
                (_: any, index: number) => `محاولة ${index + 1}`
            );
            const points = filtered.map((p: any) => Number(p.value ?? p.score ?? 0));

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

        return {
            labels: ['لا توجد بيانات'],
            datasets: [
                {
                    label: 'نتائج التدريبات',
                    data: [0],
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.12)',
                    borderWidth: 4,
                    fill: true
                }
            ]
        };
    }
}
