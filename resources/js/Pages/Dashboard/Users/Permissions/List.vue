<template>
    <Dashboard>
        <template #breadcrumb> / کاربران / سطوح دسترسی</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <jet-button ref="newPermissionButton" @keyup.enter="newPermission"
                                        @click.native="newPermission" class="m-5 sm:float-left">
                                ثبت سطح دسترسی جدید
                            </jet-button>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        کلید
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr :class="permission.parent_id==null ? 'font-bold bg-gray-200' : ''"
                                    v-for="permission in permissions" :key="permission.id">
                                    <td class="px-6 py-4 text-center text-gray-900">{{permission.no}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">{{permission.display_name}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">{{permission.name}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editPermission(permission)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.permissions.destroy',{id:permission.id})"
                                                     class="text-red-600 hover:text-red-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                        </InertiaLink>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <jet-confirmation-modal :show="submitPermissionModal" @close="submitPermissionModal = false">
                <template #title>
                    {{editPermissionModal ? 'ویرایش سطح دسترسی' : 'سطخ دسترسی جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <jet-label for="parent_id" value="دسته بندی"/>
                            <jet-select name="parent_id"
                                        ref="permission_category"
                                        class="mt-1 block w-full"
                                        v-model="permissionForm.parent_id"
                                        :options="categories"
                                        option-text="display_name"/>
                            <jet-input-error
                                :message="permissionForm.error('parent_id')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <jet-label for="display_name" value="نام سطح دسترسی"/>
                            <jet-input type="text"
                                       id="display_name"
                                       v-model="permissionForm.display_name"
                                       placeholder="نام سطح دسترسی"
                                       class="mt-1 block w-full"/>
                            <jet-input-error
                                :message="permissionForm.error('display_name')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <jet-label for="name" value="کلید سطح دسترسی"/>
                            <jet-input type="text"
                                       id="name"
                                       v-model="permissionForm.name"
                                       placeholder="کلید سطح دسترسی"
                                       class="mt-1 block w-full"/>
                            <jet-input-error
                                :message="permissionForm.error('name')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #icon>
                    <svg class="h-6 w-6 text-red-600" style="width:24px;height:24px" viewBox="0 0 24 24">
                        <path fill="currentColor"
                              d="M12.67 13.67C12.2 14.13 11.63 14.5 11 14.73V23H8V21H5V18H8V14.72C6.26 14.1 5 12.46 5 10.5C5 8 7 6 9.5 6C9.54 6 9.57 6 9.6 6C9.13 6.95 8.92 8 9.03 9.08C8.44 9.28 8 9.84 8 10.5C8 11.33 8.67 12 9.5 12C9.73 12 9.95 11.94 10.15 11.85C10.79 12.69 11.67 13.32 12.67 13.67M20.73 19.44L17.97 20.6L17.19 18.76L14.43 19.93L13.26 17.16L16.03 16L14.76 13C12.91 13.08 11.11 12.05 10.35 10.25C9.39 7.96 10.47 5.32 12.76 4.35C13 4.25 13.26 4.18 13.5 4.12C12.84 2.87 11.5 2 10 2C7.79 2 6 3.79 6 6C6 6.08 6 6.16 6 6.24C5.7 6.5 5.4 6.82 5.15 7.15C5.06 6.78 5 6.4 5 6C5 3.24 7.24 1 10 1S15 3.24 15 6C15 7.42 14.4 8.67 13.45 9.57C13.87 10 14.5 10.13 15.08 9.88C15.85 9.56 16.2 8.68 15.88 7.92C15.85 7.83 15.8 7.74 15.74 7.66C15.9 7.13 16 6.58 16 6C16 5.37 15.9 4.76 15.72 4.19C17 4.55 18.1 5.44 18.65 6.76C19.41 8.56 18.89 10.57 17.5 11.81L20.73 19.44M13 8.6C13.37 8.19 13.65 7.71 13.82 7.18C13.28 7.45 12.97 8 13 8.6Z"/>
                    </svg>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitPermissionModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editPermissionModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditPermission"
                                :class="{ 'opacity-25': permissionForm.processing }"
                                :disabled="permissionForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewPermission"
                                :class="{ 'opacity-25': permissionForm.processing }"
                                :disabled="permissionForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button'
    import JetLabel from '@/Jetstream/Label';
    import JetInput from '@/Jetstream/Input';
    import JetSelect from '@/Jetstream/Select';
    import JetInputError from '@/Jetstream/InputError';
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "List",
        components: {
            Dashboard,
            JetButton,
            JetLabel,
            JetInput,
            JetSelect,
            JetInputError,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton
        },
        props: {
            permissions: Array,
            categories: Array,
        },
        data() {
            return {
                query: '',
                submitPermissionModal: false,
                editPermissionModal: false,
                editPermissionId: '',
                permissionForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    display_name: '',
                    parent_id: null,
                }, {
                    bag: 'permissionForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.$refs.newPermissionButton.focus();
        },
        methods: {
            newPermission() {
                this.editPermissionModal = false;
                this.permissionForm = this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    display_name: '',
                    parent_id: null,
                }, {
                    bag: 'permissionForm',
                    resetOnSuccess: true
                });
                this.submitPermissionModal = true;
                this.$refs.permission_category.focus();
            },
            editPermission(permission) {
                this.permissionForm = this.$inertia.form({
                    '_method': 'PUT',
                    name: permission.name,
                    display_name: permission.display_name,
                    parent_id: permission.parent_id,
                }, {
                    bag: 'permissionForm',
                    resetOnSuccess: true
                });
                this.editPermissionId = permission.id;
                this.submitPermissionModal = true;
                this.editPermissionModal = true;
                this.$refs.permission_category.focus();
            },
            submitNewPermission() {
                this.permissionForm.post(route('dashboard.permissions.store')).then(response => {
                    if (!this.permissionForm.hasErrors()) {
                        this.submitPermissionModal = false;
                        this.$refs.newPermissionButton.focus();
                    }
                })
            },
            submitEditPermission() {
                this.permissionForm.psps = this.pspList;
                this.permissionForm.post(route('dashboard.permissions.update', {permissionId: this.editPermissionId})).then(response => {
                    if (!this.permissionForm.hasErrors()) {
                        this.submitPermissionModal = false;
                        this.editPermissionModal = false;
                    }
                })
            },
        }
    }
</script>

<style scoped>

</style>
