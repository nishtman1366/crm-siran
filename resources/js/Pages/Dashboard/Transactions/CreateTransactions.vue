<template>
    <Dashboard>
        <template #breadcrumb> / تراکنشات ماهیانه / بروزرسانی اطلاعات</template>
        <template #dashboardContent>
            <div class="text-lg m-4 border-b border-gray-300">مرحله اول</div>
            <div class="flex flex-col items-end md:flex-row">
                <div class="mx-3">
                    <jet-label>انتخاب ماه</jet-label>
                    <select name="date_id"
                            class="form-select focus:ring-indigo-500 focus:border-indigo-500"
                            id="date_id"
                            ref="date_id"
                            @change="viewSelectedDate"
                            v-model="transactionsForm.date_id">
                        <option v-for="date in dates" :key="date.id" :value="date.id">
                            {{date.name}}
                        </option>
                    </select>
                    <jet-input-error :message="transactionsForm.error('date_id')"
                                     class="mt-2"/>
                </div>
                <div
                    class="text-green-400 p-2 rounded border border-green-400 hover:text-white hover:bg-green-500 cursor-pointer"
                    v-on:click="viewCreateDateForm">
                    <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                        <path fill="currentColor"
                              d="M17,18V5H7V18L12,15.82L17,18M17,3A2,2 0 0,1 19,5V21L12,18L5,21V5C5,3.89 5.9,3 7,3H17M11,7H13V9H15V11H13V13H11V11H9V9H11V7Z"/>
                    </svg>
                </div>
                <div class="mx-3">
                    <jet-label>انتخاب سرویس دهنده</jet-label>
                    <select name="psp_id"
                            class="form-select focus:ring-indigo-500 focus:border-indigo-500"
                            id="psp_id" ref="psp_id" v-model="transactionsForm.psp_id">
                        <option v-for="psp in psps" :key="psp.id" :value="psp.id">
                            {{psp.name}}
                        </option>
                    </select>
                    <jet-input-error :message="transactionsForm.error('psp_id')"
                                     class="mt-2"/>
                </div>
                <div class="mx-3">
                    <jet-label>انتخاب فایل</jet-label>
                    <input type="file"
                           name="file"
                           ref="file"
                           v-on:change="onFileChange"/>
                    <jet-input-error :message="transactionsForm.error('file')"
                                     class="mt-2"/>
                </div>
                <jet-button v-if="date && date.status==0" @click.native="submitTransactionsForm">ارسال</jet-button>
            </div>
            <jet-section-border/>
            <div v-if="transactionsInfo && transactionsInfo.count!=0">
                <div class="text-lg m-4 border-b border-gray-300">مرحله دوم</div>
                <p class="bg-green-400 text-white m-3 px-2 py-1 text-lg rounded">مرحله اول بروزرسانی اطلاعات تراکنش ها
                    با موفقیت انجام شده است.</p>
                <div class="grid grid-cols-3 gap-3 text-lg">
                    <div class="col-span-3 text-center">تراکنشات مربوط به {{date.name}}</div>
                    <div class="text-center m-3">تعداد کل اطلاعات: {{transactionsInfo.count}} سطر</div>
                    <div class="text-center m-3">تعداد کل تراکنشات محاسبه شده: {{transactionsInfo.success}}</div>
                    <div class="text-center m-3">تعداد کل تراکنشات محاسبه نشده: {{transactionsInfo.error}}</div>
                    <div class="text-center m-3">تعداد کل تراکنشات: {{transactionsInfo.total_count}}</div>
                    <div class="text-center m-3">جمع مبلغ کل تراکنشات: {{transactionsInfo.total_amount}}</div>
                    <div class="text-center m-3">جمع کارمزد کل تراکنشات: {{transactionsInfo.total_wage}}</div>
                </div>
                <jet-section-border/>
                <p class="my-2 mx-auto bg-yellow-300 border border-gray-200 p-3 w-full md:w-3/4 lg:w-1/2 rounded">در این
                    مرحله سرجمع
                    تراکنشات صورت گرفته پذیرنده های هر کاربر محاسبه خواهد شد.</p>
                <div class="text-center">
                    <InertiaLink :href="route('dashboard.transactions.store2',{date:date.id})">
                        <jet-button class=" bg-red-500 hover:bg-red-400">شروع مرحله دوم</jet-button>
                    </InertiaLink>
                </div>
            </div>
            <jet-confirmation-modal :show="viewCreateDateModal" @close="viewCreateDateModal = false">
                <template #title>
                    ذخیره تاریخ جدید
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-green-200 border-r-4 border-green-500">
                            <p class="text-md">نام مربوط به تاریخ جدید را مانند: فروردین ۱۴۰۰ نمایید.</p>
                        </div>
                        <div>
                            <label for="date_name"
                                   class="block text-sm font-medium text-gray-700">نام تاریخ
                                پذیرندگان</label>
                            <input type="text"
                                   class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   ref="date_name"
                                   id="date_name"
                                   v-model="createDateForm.name"/>
                            <jet-input-error :message="createDateForm.error('name')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewCreateDateModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitCreateDate"
                                :class="{ 'opacity-25': createDateForm.processing }"
                                :disabled="createDateForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>
<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetLabel from "@/Jetstream/Label";
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetInputError from '@/Jetstream/InputError';
    import JetInput from '@/Jetstream/Input';
    import JetSectionBorder from '@/Jetstream/SectionBorder';
    import Pagination from "@/Pages/Dashboard/Components/Pagination";
    import {Inertia} from '@inertiajs/inertia';

    export default {
        name: "CreateTransactions",
        components: {
            Dashboard,
            JetLabel,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton,
            JetInputError,
            JetInput,
            JetSectionBorder,
            Pagination,
        },
        props: {
            dates: Array,
            psps: Array,
            dateId: Number,
            date: Object,
            transactionsInfo: Object
        },
        data() {
            return {
                viewCreateDateModal: false,

                createDateForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                }, {
                    bag: 'createDateForm',
                    resetOnSuccess: false
                }),

                transactionsForm: this.$inertia.form({
                    '_method': 'POST',
                    date_id: this.dateId,
                    psp_id: '',
                    file: '',
                }, {
                    bag: 'transactionsForm',
                    resetOnSuccess: false
                })
            }
        },
        methods: {
            viewCreateDateForm() {
                this.viewCreateDateModal = true;
            },
            submitCreateDate() {
                this.createDateForm.post(route('dashboard.transactions.dates.store'))
                    .then(response => {
                        if (!this.createDateForm.hasErrors()) {
                            this.viewCreateDateModal = false;
                        }
                    })
            },
            viewSelectedDate() {
                let element = this.$refs.date_id;
                Inertia.visit(route('dashboard.transactions.create', {date: element.options[element.selectedIndex].value}));
            },
            onFileChange(e) {
                this.transactionsForm.file = e.target.files[0];
            },
            submitTransactionsForm() {
                this.transactionsForm.post(route('dashboard.transactions.store'))
                    .then(response => {
                        if (!this.transactionsForm.hasErrors()) {

                        }
                    });
            },
        }
    }
</script>

<style scoped>

</style>
