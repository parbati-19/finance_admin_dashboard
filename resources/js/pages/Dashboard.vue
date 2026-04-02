<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { dashboard } from '@/routes';

// Define types
interface StatCard {
  title: string;
  value: string;
  change: string;
  trend: 'up' | 'down';
  description: string;
  footer: string;
}

interface Section {
  id: number;
  header: string;
  sectionType: string;
  status: 'Done' | 'In Process';
  target: string;
  limit: string;
  reviewer: string;
}

interface ChartDataPoint {
  date: string;
  desktop: number;
  mobile: number;
}

// Layout options
defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Dashboard',
        href: dashboard(),
      },
    ],
  },
});

// Stats data
const stats = ref<StatCard[]>([
  {
    title: 'Total Revenue',
    value: '$1,250.00',
    change: '+12.5%',
    trend: 'up',
    description: 'Trending up this month',
    footer: 'Visitors for the last 6 months'
  },
  {
    title: 'New Customers',
    value: '1,234',
    change: '-20%',
    trend: 'down',
    description: 'Down 20% this period',
    footer: 'Acquisition needs attention'
  },
  {
    title: 'Active Accounts',
    value: '45,678',
    change: '+12.5%',
    trend: 'up',
    description: 'Strong user retention',
    footer: 'Engagement exceed targets'
  },
  {
    title: 'Growth Rate',
    value: '4.5%',
    change: '+4.5%',
    trend: 'up',
    description: 'Steady performance increase',
    footer: 'Meets growth projections'
  }
]);

// Chart data
const chartData = ref<ChartDataPoint[]>([
  { date: 'Jun 24', desktop: 850, mobile: 450 },
  { date: 'Jun 25', desktop: 434, mobile: 380 },
  { date: 'Jun 26', desktop: 900, mobile: 420 },
  { date: 'Jun 27', desktop: 780, mobile: 510 },
  { date: 'Jun 28', desktop: 820, mobile: 490 },
  { date: 'Jun 29', desktop: 650, mobile: 320 },
  { date: 'Jun 30', desktop: 950, mobile: 600 }
]);

const selectedTimeRange = ref<'7d' | '30d' | '90d'>('7d');

// Table data
const sections = ref<Section[]>([
  {
    id: 1,
    header: 'Cover page',
    sectionType: 'Cover page',
    status: 'In Process',
    target: '',
    limit: '',
    reviewer: 'Eddie Lake'
  },
  {
    id: 2,
    header: 'Table of contents',
    sectionType: 'Table of contents',
    status: 'Done',
    target: '',
    limit: '',
    reviewer: 'Eddie Lake'
  },
  {
    id: 3,
    header: 'Executive summary',
    sectionType: 'Narrative',
    status: 'Done',
    target: '',
    limit: '',
    reviewer: 'Eddie Lake'
  },
  {
    id: 4,
    header: 'Technical approach',
    sectionType: 'Narrative',
    status: 'Done',
    target: '',
    limit: '',
    reviewer: 'Jamik Tashpulatov'
  },
  {
    id: 5,
    header: 'Design',
    sectionType: 'Narrative',
    status: 'In Process',
    target: '',
    limit: '',
    reviewer: 'Jamik Tashpulatov'
  },
  {
    id: 6,
    header: 'Capabilities',
    sectionType: 'Narrative',
    status: 'In Process',
    target: '',
    limit: '',
    reviewer: 'Jamik Tashpulatov'
  },
  {
    id: 7,
    header: 'Integration with existing systems',
    sectionType: 'Narrative',
    status: 'In Process',
    target: '',
    limit: '',
    reviewer: 'Jamik Tashpulatov'
  },
  {
    id: 8,
    header: 'Innovation and Advantages',
    sectionType: 'Narrative',
    status: 'Done',
    target: '',
    limit: '',
    reviewer: ''
  },
  {
    id: 9,
    header: 'Overview of EMR\'s Innovative Solutions',
    sectionType: 'Technical content',
    status: 'Done',
    target: '',
    limit: '',
    reviewer: ''
  },
  {
    id: 10,
    header: 'Advanced Algorithms and Machine Learning',
    sectionType: 'Narrative',
    status: 'Done',
    target: '',
    limit: '',
    reviewer: ''
  }
]);

const selectedTab = ref('outline');
const selectedSections = ref<number[]>([]);
const currentPage = ref(1);
const rowsPerPage = ref(10);

// Computed
const paginatedSections = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage.value;
  const end = start + rowsPerPage.value;
  return sections.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(sections.value.length / rowsPerPage.value);
});

const allSelected = computed({
  get: () => selectedSections.value.length === paginatedSections.value.length && paginatedSections.value.length > 0,
  set: (value: boolean) => {
    if (value) {
      selectedSections.value = paginatedSections.value.map(s => s.id);
    } else {
      selectedSections.value = [];
    }
  }
});

// Methods
const toggleSelection = (id: number) => {
  const index = selectedSections.value.indexOf(id);
  if (index > -1) {
    selectedSections.value.splice(index, 1);
  } else {
    selectedSections.value.push(id);
  }
};

const isSelected = (id: number) => {
  return selectedSections.value.includes(id);
};

const updateTimeRange = (range: '7d' | '30d' | '90d') => {
  selectedTimeRange.value = range;
  // Here you would fetch new data based on the selected range
};

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const addSection = () => {
  // Add new section logic
  console.log('Add section');
};

const updateSection = (id: number, field: string, value: string) => {
  const section = sections.value.find(s => s.id === id);
  if (section) {
    (section as any)[field] = value;
  }
};
</script>

<template>
  <Head title="Dashboard" />

  <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <div class="flex flex-col gap-4 py-4 md:gap-6 md:py-6">

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 gap-4 px-4 lg:px-6 xl:grid-cols-2 2xl:grid-cols-4">
        <div
          v-for="stat in stats"
          :key="stat.title"
          class="flex flex-col gap-6 rounded-xl border bg-card py-6 shadow-sm"
        >
          <div class="grid grid-cols-[1fr_auto] gap-1.5 px-6">
            <p class="text-sm text-muted-foreground">{{ stat.title }}</p>
            <h3 class="text-2xl font-semibold tabular-nums sm:text-3xl">
              {{ stat.value }}
            </h3>
            <div class="col-start-2 row-span-2 row-start-1 self-start justify-self-end">
              <div
                class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium"
                :class="stat.trend === 'up' ? 'text-green-600' : 'text-red-600'"
              >
                <svg
                  v-if="stat.trend === 'up'"
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 17l6 -6l4 4l8 -8"></path>
                  <path d="M14 7l7 0l0 7"></path>
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 7l6 6l4 -4l8 8"></path>
                  <path d="M21 10l0 7l-7 0"></path>
                </svg>
                {{ stat.change }}
              </div>
            </div>
          </div>
          <div class="flex flex-col gap-1.5 px-6 text-sm">
            <div class="flex items-center gap-2 font-medium">
              {{ stat.description }}
              <svg
                v-if="stat.trend === 'up'"
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-4 w-4"
              >
                <path d="M3 17l6 -6l4 4l8 -8"></path>
                <path d="M14 7l7 0l0 7"></path>
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-4 w-4"
              >
                <path d="M3 7l6 6l4 -4l8 8"></path>
                <path d="M21 10l0 7l-7 0"></path>
              </svg>
            </div>
            <div class="text-muted-foreground">{{ stat.footer }}</div>
          </div>
        </div>
      </div>

      <!-- Chart Card -->
      <div class="px-4 lg:px-6">
        <div class="flex flex-col gap-6 rounded-xl border bg-card py-6 shadow-sm">
          <div class="flex items-start justify-between px-6">
            <div>
              <h3 class="font-semibold leading-none">Total Visitors</h3>
              <p class="mt-1.5 text-sm text-muted-foreground">
                <span class="hidden sm:inline">Total for the last 3 months</span>
                <span class="sm:hidden">Last 3 months</span>
              </p>
            </div>
            <div class="flex gap-2">
              <button
                v-for="range in [
                  { value: '90d', label: 'Last 3 months' },
                  { value: '30d', label: 'Last 30 days' },
                  { value: '7d', label: 'Last 7 days' }
                ]"
                :key="range.value"
                @click="updateTimeRange(range.value as '7d' | '30d' | '90d')"
                class="h-9 rounded-md border px-3 text-sm transition-colors hover:bg-accent"
                :class="selectedTimeRange === range.value ? 'bg-accent' : 'bg-transparent'"
              >
                {{ range.label }}
              </button>
            </div>
          </div>
          <div class="px-6">
            <!-- Chart placeholder - you would integrate a real chart library here -->
            <div class="flex h-[250px] items-center justify-center rounded-lg border border-dashed">
              <p class="text-sm text-muted-foreground">
                Chart Component (integrate Recharts, Chart.js, or similar)
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs Section -->
      <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between px-4 lg:px-6">
          <div class="flex h-9 items-center justify-center rounded-lg bg-muted p-1">
            <button
              v-for="tab in [
                { value: 'outline', label: 'Outline' },
                { value: 'past-performance', label: 'Past Performance', badge: 3 },
                { value: 'key-personnel', label: 'Key Personnel', badge: 2 },
                { value: 'focus-documents', label: 'Focus Documents' }
              ]"
              :key="tab.value"
              @click="selectedTab = tab.value"
              class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-2 py-1 text-sm font-medium transition-colors"
              :class="selectedTab === tab.value
                ? 'border-input bg-background shadow-sm'
                : 'text-muted-foreground hover:text-foreground'"
            >
              {{ tab.label }}
              <span
                v-if="tab.badge"
                class="flex h-5 w-5 items-center justify-center rounded-full bg-secondary text-xs"
              >
                {{ tab.badge }}
              </span>
            </button>
          </div>

          <div class="flex items-center gap-2">
            <button class="flex h-8 items-center gap-1.5 rounded-md border bg-background px-3 text-sm shadow-sm hover:bg-accent">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                <path d="M12 4l0 16"></path>
              </svg>
              <span class="hidden lg:inline">Customize Columns</span>
              <span class="lg:hidden">Columns</span>
            </button>
            <button
              @click="addSection"
              class="flex h-8 items-center gap-1.5 rounded-md border bg-background px-3 text-sm shadow-sm hover:bg-accent"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
              </svg>
              <span class="hidden lg:inline">Add Section</span>
            </button>
          </div>
        </div>

        <!-- Table -->
        <div v-if="selectedTab === 'outline'" class="overflow-hidden rounded-lg border px-4 lg:px-6">
          <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
              <thead class="sticky top-0 z-10 border-b bg-muted">
                <tr class="border-b transition-colors hover:bg-muted/50">
                  <th class="h-10 px-2 text-left align-middle font-medium">
                    <!-- Drag handle -->
                  </th>
                  <th class="h-10 px-2 text-left align-middle font-medium">
                    <input
                      type="checkbox"
                      :checked="allSelected"
                      @change="allSelected = !allSelected"
                      class="h-4 w-4 rounded border-input"
                    />
                  </th>
                  <th class="h-10 px-2 text-left align-middle font-medium">Header</th>
                  <th class="h-10 px-2 text-left align-middle font-medium">Section Type</th>
                  <th class="h-10 px-2 text-left align-middle font-medium">Status</th>
                  <th class="h-10 px-2 text-left align-middle font-medium">
                    <div class="text-right">Target</div>
                  </th>
                  <th class="h-10 px-2 text-left align-middle font-medium">
                    <div class="text-right">Limit</div>
                  </th>
                  <th class="h-10 px-2 text-left align-middle font-medium">Reviewer</th>
                  <th class="h-10 px-2 text-left align-middle font-medium"></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="section in paginatedSections"
                  :key="section.id"
                  class="border-b transition-colors hover:bg-muted/50"
                >
                  <td class="p-2 align-middle">
                    <button class="flex h-7 w-7 items-center justify-center hover:bg-transparent">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12"
                        height="12"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="text-muted-foreground"
                      >
                        <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                      </svg>
                    </button>
                  </td>
                  <td class="p-2 align-middle">
                    <input
                      type="checkbox"
                      :checked="isSelected(section.id)"
                      @change="toggleSelection(section.id)"
                      class="h-4 w-4 rounded border-input"
                    />
                  </td>
                  <td class="p-2 align-middle">{{ section.header }}</td>
                  <td class="p-2 align-middle">
                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs">
                      {{ section.sectionType }}
                    </span>
                  </td>
                  <td class="p-2 align-middle">
                    <span
                      class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs"
                      :class="section.status === 'Done' ? 'text-green-600' : ''"
                    >
                      <svg
                        v-if="section.status === 'Done'"
                        xmlns="http://www.w3.org/2000/svg"
                        width="12"
                        height="12"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                      >
                        <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z"></path>
                      </svg>
                      <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        width="12"
                        height="12"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <path d="M12 6l0 -3"></path>
                        <path d="M16.25 7.75l2.15 -2.15"></path>
                        <path d="M18 12l3 0"></path>
                        <path d="M16.25 16.25l2.15 2.15"></path>
                        <path d="M12 18l0 3"></path>
                        <path d="M7.75 16.25l-2.15 2.15"></path>
                        <path d="M6 12l-3 0"></path>
                        <path d="M7.75 7.75l-2.15 -2.15"></path>
                      </svg>
                      {{ section.status }}
                    </span>
                  </td>
                  <td class="p-2 align-middle">
                    <input
                      type="text"
                      :value="section.target"
                      @input="updateSection(section.id, 'target', ($event.target as HTMLInputElement).value)"
                      class="h-8 w-16 rounded-md border border-transparent bg-transparent px-3 text-right text-sm outline-none transition-colors hover:bg-input/30 focus:border-input focus:bg-background"
                    />
                  </td>
                  <td class="p-2 align-middle">
                    <input
                      type="text"
                      :value="section.limit"
                      @input="updateSection(section.id, 'limit', ($event.target as HTMLInputElement).value)"
                      class="h-8 w-16 rounded-md border border-transparent bg-transparent px-3 text-right text-sm outline-none transition-colors hover:bg-input/30 focus:border-input focus:bg-background"
                    />
                  </td>
                  <td class="p-2 align-middle">
                    <span v-if="section.reviewer">{{ section.reviewer }}</span>
                    <select
                      v-else
                      class="h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                      @change="updateSection(section.id, 'reviewer', ($event.target as HTMLSelectElement).value)"
                    >
                      <option value="">Assign reviewer</option>
                      <option value="Eddie Lake">Eddie Lake</option>
                      <option value="Jamik Tashpulatov">Jamik Tashpulatov</option>
                    </select>
                  </td>
                  <td class="p-2 align-middle">
                    <button class="flex h-8 w-8 items-center justify-center rounded-md hover:bg-accent">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                      >
                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between py-4">
            <div class="hidden text-sm text-muted-foreground lg:flex">
              {{ selectedSections.length }} of {{ sections.length }} row(s) selected.
            </div>
            <div class="flex w-full items-center gap-8 lg:w-fit">
              <div class="hidden items-center gap-2 lg:flex">
                <label class="text-sm font-medium">Rows per page</label>
                <select
                  v-model="rowsPerPage"
                  class="h-8 w-20 rounded-md border bg-transparent px-3 text-sm"
                >
                  <option :value="10">10</option>
                  <option :value="20">20</option>
                  <option :value="50">50</option>
                </select>
              </div>
              <div class="flex items-center justify-center text-sm font-medium">
                Page {{ currentPage }} of {{ totalPages }}
              </div>
              <div class="ml-auto flex items-center gap-2 lg:ml-0">
                <button
                  @click="goToPage(1)"
                  :disabled="currentPage === 1"
                  class="hidden h-8 w-8 items-center justify-center rounded-md border bg-background shadow-sm hover:bg-accent disabled:opacity-50 lg:flex"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 7l-5 5l5 5"></path>
                    <path d="M17 7l-5 5l5 5"></path>
                  </svg>
                </button>
                <button
                  @click="goToPage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  class="flex h-8 w-8 items-center justify-center rounded-md border bg-background shadow-sm hover:bg-accent disabled:opacity-50"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 6l-6 6l6 6"></path>
                  </svg>
                </button>
                <button
                  @click="goToPage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  class="flex h-8 w-8 items-center justify-center rounded-md border bg-background shadow-sm hover:bg-accent disabled:opacity-50"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 6l6 6l-6 6"></path>
                  </svg>
                </button>
                <button
                  @click="goToPage(totalPages)"
                  :disabled="currentPage === totalPages"
                  class="hidden h-8 w-8 items-center justify-center rounded-md border bg-background shadow-sm hover:bg-accent disabled:opacity-50 lg:flex"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M7 7l5 5l-5 5"></path>
                    <path d="M13 7l5 5l-5 5"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Other tabs content -->
        <div v-else class="px-4 lg:px-6">
          <div class="flex h-64 items-center justify-center rounded-lg border border-dashed">
            <p class="text-sm text-muted-foreground">{{ selectedTab }} content</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Additional custom styles if needed */
</style>
