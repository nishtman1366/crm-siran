<template>
    <Dashboard>
        <template #breadcrumb> / ویرایش اطلاعات {{selectedUser.name}}</template>
        <template #dashboardContent>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0 text-justify">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">ویرایش اطلاعات کاربر</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به کاربر را وارد نمایید.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت نام کاربری، یک نام کاربری که از حروف انگلیسی تشکیل شده است را نماید. نام کاربری
                                نباید قبلا برای کاربر دیگری استفاده شده باشد.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت کلمه عبور، یک کلمه عبور برای کاربر جدید وارد نماید. بهتر است کلمه عبور انتخابی
                                پیچیدگی لازم جهت جلوگیری از رخنه های امنیتی را داشته باشد.
                                <span class="text-red-800">توجه فرمایید که فقط در صورتی که میخواهید کلمه عبور کاربر را تغییر دهید از این بخش استفاده کنید و در غیر این صورت فیلد کلمه عبور را به حال خود رها کنید.</span>
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت تلفن تماس، شماره تلفن تماس کاربر جدید وارد نماید. بهتر است در این قسمت شماره
                                موبایل کاربر را وارد نمایید که در صورت نیاز اطلاع رسانی از طریق پیامک صورت گیرد.
                            </p>
                            <p v-if="$page.user.level=='SUPERUSER' || (selectedUser.parent && selectedUser.parent.percent!=0)" class="mt-5 text-sm text-red-500">کارمزد تراکنشات باید عددی بین ۰ و ۱۰۰ باشد. دقت داشته باشید این عدد هنگام بروزرسانی ماهیانه مبلغ کارمزد تراکنشات بوسیله سیستم مورد استفاده قرار خواهد گرفت.</p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid sm:grid-cols-6 gap-6">
                                    <div class="col-3 sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            نام:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام"
                                               ref="name"
                                               id="name"
                                               v-model="userForm.name"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('name')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="mobile" class="block text-sm font-medium text-gray-700">
                                            تلفن تماس:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تلفن تماس"
                                               ref="mobile"
                                               id="mobile"
                                               v-model="userForm.mobile"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('mobile')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="username" class="block text-sm font-medium text-gray-700">
                                            نام کاربری:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کاربری"
                                               ref="username"
                                               id="username"
                                               v-model="userForm.username"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('username')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            کلمه عبور:
                                        </label>
                                        <input type="password"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کلمه عبور"
                                               ref="password"
                                               id="password"
                                               v-model="userForm.password"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('password')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="$page.user.level==='ADMIN' && type==='marketer'"
                                         class="col-2 sm:col-span-2">
                                        <label for="parent_id" class="block text-sm font-medium text-gray-700">
                                            نماینده:
                                        </label>
                                        <select id="parent_id" name="parent_id" ref="parent_id"
                                                v-model="userForm.parent_id"
                                                autocomplete="status"
                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value="$page.user.id">خودم</option>
                                            <option v-for="agent in agents" :key="agent.id"
                                                    :value="agent.id">
                                                {{agent.name}}
                                            </option>
                                        </select>
                                        <jet-input-error :message="userForm.error('parent_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="status" class="block text-sm font-medium text-gray-700">
                                            وضعیت:
                                        </label>
                                        <select id="status" name="status" ref="status"
                                                v-model="userForm.status"
                                                autocomplete="status"
                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option v-for="status in statuses" :key="status.id"
                                                    :value="status.id">
                                                {{status.name}}
                                            </option>
                                        </select>
                                        <jet-input-error :message="userForm.error('status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="sm:col-span-6 border-b border-gray-500">

                                    </div>
                                    <div v-if="type=='admin'" class="sm:col-span-3">
                                        <label for="company_name" class="block text-sm font-medium text-gray-700">
                                            نام شرکت:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام شرکت"
                                               ref="company_name"
                                               id="company_name"
                                               v-model="userForm.company_name"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('company_name')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="type=='admin'" class="sm:col-span-3">
                                        <input type="file" class="hidden"
                                               ref="photo"
                                               @change="updatePhotoPreview">
                                        <div class="mt-2" v-show="! photoPreview">
                                            <img :src="selectedUser.companyLogoUrl" alt="Current Company Logo"
                                                 class="rounded-full h-20 w-20 object-cover">
                                        </div>
                                        <div class="mt-2" v-show="photoPreview">
                                            <span class="block rounded-full w-20 h-20"
                                                  :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                                            </span>
                                        </div>
                                        <jet-secondary-button class="mt-2 mr-2" type="button"
                                                              @click.native.prevent="selectNewPhoto">
                                            انتخاب لوگوی شرکت
                                        </jet-secondary-button>

                                        <jet-secondary-button type="button" class="mt-2"
                                                              @click.native.prevent="deletePhoto"
                                                              v-if="selectedUser.companyLogoUrl">
                                            حذف تصویر کنونی
                                        </jet-secondary-button>
                                    </div>
                                    <div v-if="$page.user.level=='SUPERUSER' || (selectedUser.parent && selectedUser.parent.percent!=0)"
                                         class="sm:col-span-3">
                                        <label for="percent" class="block text-sm font-medium text-gray-700">
                                            کارمزد تراکنشات:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کارمزد"
                                               ref="percent"
                                               id="percent"
                                               v-model="userForm.percent"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('percent')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-6 sm:col-span-6 text-left">
                                        <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                v-on:click="submitUserForm">
                                            ذخیره اطلاعات
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button';
    import JetInputError from '@/Jetstream/InputError';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "EditUser",
        components: {Dashboard, JetButton, JetInputError, JetSecondaryButton},
        props: {
            type: String,
            userType: String,
            statuses: String,
            agents: Array,
            selectedUser: Object
        },
        data() {
            return {
                photoPreview: null,
                userForm: this.$inertia.form({
                    '_method': 'PUT',
                    parent_id: this.selectedUser.parent_id,
                    name: this.selectedUser.name,
                    username: this.selectedUser.username,
                    password: '',
                    mobile: this.selectedUser.mobile,
                    status: this.selectedUser.status,
                    company_name: this.selectedUser.company_name,
                    percent: this.selectedUser.percent,
                    companyLogo: null,
                    deleteCompanyLogo: false,
                }, {
                    bag: 'userForm',
                    resetOnSuccess: false
                })
            }
        },
        mounted() {

        },
        methods: {
            selectNewPhoto() {
                this.$refs.photo.click();
            },
            updatePhotoPreview() {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(this.$refs.photo.files[0]);
                this.userForm.deleteCompanyLogo = false;
            },
            deletePhoto() {
                this.userForm.deleteCompanyLogo = true;
                this.selectedUser.companyLogoUrl = null
                this.photoPreview = null
            },
            submitUserForm() {
                if (this.$refs.photo) {
                    this.userForm.companyLogo = this.$refs.photo.files[0]
                }
                if (this.userForm.password === '') delete this.userForm.password;
                this.userForm.post(route('dashboard.users.update', {id: this.selectedUser.id, type: this.type}), {
                    preserveScroll: true
                }).then(response => {

                })
            }
        }
    }
</script>

<style scoped>

</style>
