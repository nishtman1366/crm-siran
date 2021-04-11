<template>
    <Dashboard>
        <template #breadcrumb> / تراکنشات / گزارش ماهیانه</template>
        <template #dashboardContent>
            <div class="flex flex-wrap items-end">
                <div class="mx-2">
                    <jet-label>دوره</jet-label>
                    <select name="date_id" ref="date_id" v-model="date_id" class="form-select">
                        <option :value="0">انتخاب کنید:</option>
                        <option v-for="date in dates" :key="date.id" :value="date.id">
                            {{date.name}}
                        </option>
                    </select>
                </div>
                <div class="mx-2" v-if="$page.user.level=='SUPERUSER'">
                    <jet-label>مدیران</jet-label>
                    <select name="admin_id" ref="admin_id" v-model="admin_id" class="form-select">
                        <option :value="0">انتخاب کنید:</option>
                        <option v-for="admin in admins" :key="admin.id" :value="admin.id">
                            {{admin.name}}
                        </option>
                    </select>
                </div>
                <div class="mx-2" v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                    <jet-label>نمایندگان</jet-label>
                    <select name="agent_id" ref="agent_id" v-model="agent_id" class="form-select">
                        <option :value="0">انتخاب کنید:</option>
                        <option v-for="agent in agentsList" :key="agent.id" :value="agent.id">
                            {{agent.name}}
                        </option>
                    </select>
                </div>
                <div class="mx-2"
                     v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT'">
                    <jet-label>بازاریابان</jet-label>
                    <select name="marketer_id" ref="marketer_id" v-model="marketer_id" class="form-select">
                        <option :value="0">انتخاب کنید:</option>
                        <option v-for="marketer in marketersList" :key="marketer.id" :value="marketer.id">
                            {{marketer.name}}
                        </option>
                    </select>
                </div>
                <div class="mx-2">
                    <jet-button @click.native="viewTransActions">مشاهده</jet-button>
                </div>
            </div>
            <template v-if="date && date!=0">
                <jet-section-border/>
                <div v-if="selectedDate.status==0" class="flex items-center px-2 py-1 bg-blue-300 text-blue-600 m-3 rounded text-lg font-bold">
                    <div>
                        <i class="material-icons text-center text-xl leading-5 align-middle mx-3">check</i>
                    </div>
                    <p class="">اطلاعات تراکنشات
                        مربوط به
                        {{selectedDate.name}}
                        وارد نشده است.</p>
                </div>
                <template v-else>
                    <template v-if="transactions && transactions.length > 0">
                        <div class="border-2 border-gray-300 rounded m-2 p-2"
                             v-for="transaction in transactions"
                             :key="transaction.id">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="md:col-span-4">
                                    <div class="text-center text-md text-indigo-500">
                                        نام کاربر: {{transaction.user && transaction.user.name}}
                                        ({{transaction.user && transaction.user.levelText}})
                                        <template v-if="transaction.user.level==='AGENT'">
                                            | نام مدیر: {{transaction.user.parent && transaction.user.parent.name}}
                                            ({{transaction.user.parent && transaction.user.parent.levelText}})
                                        </template>
                                        <template v-if="transaction.user.level==='MARKETER'">
                                            | نام نماینده: {{transaction.user.parent && transaction.user.parent.name}}
                                            | نام مدیر: {{transaction.user.parent && transaction.user.parent.parent &&
                                            transaction.user.parent.parent.name}}
                                            ({{transaction.user.parent && transaction.user.parent.parent &&
                                            transaction.user.parent.parent.levelText}})
                                        </template>
                                    </div>
                                    <div
                                        class="col-span-2 md:col-span-6 lg:col-span-3 text-center text-lg text-indigo-500">
                                        تاریخ: {{transaction.date && transaction.date.name}}
                                    </div>
                                </div>
                                <div class="col-span-2 md:col-span-4">
                                    <div
                                        class="flex flex-wrap -m-1 py-5 gap-3 border-b border-gray-300 text-lg font-bold justify-center">
                                        <div
                                            class="text-center text-blue-500 rounded-full border border-blue-500 px-3 py-1">
                                            تعداد
                                            پرونده ها: {{transaction.profiles_count | persianDigit}}
                                        </div>
                                        <div
                                            class="text-center text-green-500 rounded-full border border-green-500 px-3 py-1">
                                            پرونده های سالم: {{transaction.success | persianDigit}}
                                        </div>
                                        <div
                                            class="text-center text-red-500 rounded-full border border-red-500 px-3 py-1">
                                            پرونده
                                            های ناقص: {{transaction.error | persianDigit}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-2 md:col-span-1 space-y-3 border-l border-gray-300 px-1">
                                    <div class="text-center bg-green-300 font-bold px-2 py-1 rounded">مجموع کارمزد
                                        دریافتی:{{transaction.superuser_wage | persianDigit}}
                                    </div>
                                    <div class="text-center bg-yellow-300 font-bold px-2 py-1 rounded">سهم
                                        مدیر:{{transaction.admin_wage | persianDigit}}
                                    </div>
                                    <div class="text-center bg-blue-300 font-bold px-2 py-1 rounded">سهم
                                        نماینده:{{transaction.agent_wage | persianDigit}}
                                    </div>
                                    <div class="text-center bg-red-300 font-bold px-2 py-1 rounded">سهم
                                        بازاریاب:{{transaction.marketer_wage | persianDigit}}
                                    </div>
                                </div>
                                <div class="col-span-2 md:col-span-3">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                انواع تراکنش
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                مانده گیری
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                شارژ
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                قبض
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                خرید
                                            </th>
                                            <th scope="col" class="px-6 py-3 bg-gray-50">جمع کل</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr class=" hover:bg-gray-100">
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                جمع مبلغ
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.balance_amount | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.charge_amount | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.bills_amount | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.buys_amount | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.total_amount | persianDigit}}
                                            </td>
                                        </tr>
                                        <tr class=" hover:bg-gray-100">
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                تعداد
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.balance_count | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.charge_count | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.bills_count | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.buys_count | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.total_count | persianDigit}}
                                            </td>
                                        </tr>
                                        <tr class=" hover:bg-gray-100">
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                کارمزد
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.balance_wage | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.charge_wage | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.bills_wage | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.buys_wage | persianDigit}}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-900">
                                                {{transaction.total_wage | persianDigit}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div v-else class="flex items-center px-2 py-1 bg-indigo-300 text-indigo-600 m-3 rounded text-lg font-bold">
                        <div>
                            <i class="material-icons text-center text-xl leading-5 align-middle mx-3">check</i>
                        </div>
                        <p class="">اطلاعات تراکنشات
                            مربوط به
                            {{selectedDate.name}}
                            وارد نشده است.</p>
                    </div>
                </template>
            </template>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetSectionBorder from "@/Jetstream/SectionBorder"
    import JetLabel from "@/Jetstream/Label"
    import JetButton from "@/Jetstream/Button"
    import {Inertia} from '@inertiajs/inertia';

    export default {
        name: "List",
        components: {Dashboard, JetSectionBorder, JetLabel, JetButton},
        props: {
            admins: Array,
            agents: Array,
            marketers: Array,
            dates: Array,
            transactions: Array,
            date: Number,
            adminId: Number,
            agentId: Number,
            marketerId: Number,
        },
        data() {
            return {
                date_id: this.date ? this.date : 0,
                admin_id: this.adminId ? this.adminId : (this.$page.user.level === 'ADMIN' ? this.$page.user.id : 0),
                agent_id: this.agentId ? this.agentId : (this.$page.user.level === 'AGENT' ? this.$page.user.id : 0),
                marketer_id: this.marketerId ? this.marketerId : (this.$page.user.level === 'MARKETER' ? this.$page.user.id : 0),

                adminsList: [],
                agentsList: [],
                marketersList: []
            }
        },
        mounted() {
            this.computeAgents(this.admin_id);
            this.computeMarketers(this.admin_id);
        },
        computed: {
            selectedDate: function () {
                let date = this.dates.filter(d => {
                    return d.id == this.date;
                });
                if (date.length > 0) return date[0];
            }
        },
        watch: {
            admin_id: function ($val) {
                this.computeAgents($val);
                this.computeMarketers($val);
            },
            agent_id: function ($val) {
                this.computeMarketers($val);
            }
        },
        methods: {
            viewTransActions() {
                Inertia.visit(route('dashboard.transactions.list', {
                    dateId: this.date_id,
                    adminId: this.admin_id,
                    agentId: this.agent_id,
                    marketerId: this.marketer_id,
                }))
            },
            computeAgents(userId) {
                if (userId !== 0) {
                    this.agentsList = this.agents.filter(agent => {
                        return agent.parent.id === userId;
                    })
                } else {
                    this.agentsList = this.agents.filter(agent => {
                        return agent.parent.id === this.$page.user.id;
                    })
                }
            },
            computeMarketers(userId) {
                if (userId !== 0) {
                    this.marketersList = this.marketers.filter(marketer => {
                        return marketer.parent.id === userId;
                    })
                } else {
                    this.marketersList = this.marketers.filter(marketer => {
                        return marketer.parent.id === this.$page.user.id;
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>
