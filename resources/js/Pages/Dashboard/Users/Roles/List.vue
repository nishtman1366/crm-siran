<template>
    <Dashboard>
        <template #breadcrumb> / کاربران / گروه های کاربری</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <jet-button class="m-5 sm:float-left">
                                ثبت گروه کاربری جدید
                            </jet-button>
                            <inertia-link :href="route('dashboard.permissions.list')">
                                <jet-button class="bg-indigo-500 hover:bg-indigo-400 m-5 sm:float-left">
                                    سطوح دسترسی
                                </jet-button>
                            </inertia-link>
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
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تعداد کاربران
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="role in roles" :key="role.id">
                                    <td class="px-6 py-4 text-center text-gray-900">{{role.no}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">{{role.display_name}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">{{role.name}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">{{role.users_count}}</td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-green-600 hover:text-green-900"
                                                v-on:click="viewPermissionModal(role)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M12.67 13.67C12.2 14.13 11.63 14.5 11 14.73V23H8V21H5V18H8V14.72C6.26 14.1 5 12.46 5 10.5C5 8 7 6 9.5 6C9.54 6 9.57 6 9.6 6C9.13 6.95 8.92 8 9.03 9.08C8.44 9.28 8 9.84 8 10.5C8 11.33 8.67 12 9.5 12C9.73 12 9.95 11.94 10.15 11.85C10.79 12.69 11.67 13.32 12.67 13.67M20.73 19.44L17.97 20.6L17.19 18.76L14.43 19.93L13.26 17.16L16.03 16L14.76 13C12.91 13.08 11.11 12.05 10.35 10.25C9.39 7.96 10.47 5.32 12.76 4.35C13 4.25 13.26 4.18 13.5 4.12C12.84 2.87 11.5 2 10 2C7.79 2 6 3.79 6 6C6 6.08 6 6.16 6 6.24C5.7 6.5 5.4 6.82 5.15 7.15C5.06 6.78 5 6.4 5 6C5 3.24 7.24 1 10 1S15 3.24 15 6C15 7.42 14.4 8.67 13.45 9.57C13.87 10 14.5 10.13 15.08 9.88C15.85 9.56 16.2 8.68 15.88 7.92C15.85 7.83 15.8 7.74 15.74 7.66C15.9 7.13 16 6.58 16 6C16 5.37 15.9 4.76 15.72 4.19C17 4.55 18.1 5.44 18.65 6.76C19.41 8.56 18.89 10.57 17.5 11.81L20.73 19.44M13 8.6C13.37 8.19 13.65 7.71 13.82 7.18C13.28 7.45 12.97 8 13 8.6Z"/>
                                            </svg>
                                        </button>
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editRole(role)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.roles.destroy',{id:role.id})"
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

            <jet-confirmation-modal :show="permissionsModal" @close="permissionsModal = false">
                <template #title>سطح دسترسی "{{permissionsForm.name}}"</template>
                <template #content>
                    <div class="mt-2 h-64 overflow-y-auto">
                        <div v-for="category in categories" class="border-b border-gray-200 py-3" :key="category.id">
                            <p class="font-bold">
                                <jet-checkbox @click.native="togglePermission(category.id)"
                                              :id="'permission_'+category.id"
                                              class="mx-2"
                                              :checked="permissionsForm.permissions.indexOf(category.id)!==-1"
                                              :value="category.id"/>
                                <jet-label class="inline font-bold" :for="'permission_'+category.id"
                                           :value="category.display_name"/>
                            </p>
                            <div class="flex flex-wrap">
                                <p class="my-2" v-for="permission in category.children" :key="permission.id">
                                    <jet-checkbox @click.native="togglePermission(permission.id)"
                                                  :id="'permission_'+permission.id"
                                                  class="mx-2"
                                                  :checked="permissionsForm.permissions.indexOf(permission.id)!==-1"
                                                  :value="permission.id"/>
                                    <jet-label class="inline" :for="'permission_'+permission.id"
                                               :value="permission.display_name"/>
                                </p>
                            </div>
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
                    <jet-secondary-button class="ml-2" @click.native="permissionsModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitPermissions"
                                :class="{ 'opacity-25': permissionsForm.processing }"
                                :disabled="permissionsForm.processing">
                        ذخیره
                    </jet-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from "@/Jetstream/Button"
    import JetLabel from '@/Jetstream/Label';
    import JetInput from '@/Jetstream/Input';
    import JetSelect from '@/Jetstream/Select';
    import JetInputError from '@/Jetstream/InputError';
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetCheckbox from '@/Jetstream/Checkbox'

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
            JetSecondaryButton,
            JetCheckbox
        },
        props: {
            roles: Array,
            categories: Array
        },
        data() {
            return {
                permissionsModal: false,
                permissionsForm: this.$inertia.form({
                    role_id: '',
                    name: '',
                    permissions: [],
                })
            }
        },
        methods: {
            viewPermissionModal(role) {
                this.permissionsModal = true;
                this.permissionsForm.role_id = role.id;
                this.permissionsForm.name = role.display_name;
                for (let permission of role.permissions) {
                    this.permissionsForm.permissions.push(permission.id);
                }
            },
            togglePermission(id) {
                let index = this.permissionsForm.permissions.indexOf(id);
                if (index === -1) {
                    this.permissionsForm.permissions.push(id);
                } else {
                    this.permissionsForm.permissions.splice(index, 1);
                }
            },
            submitPermissions() {
                this.permissionsForm.post(route('dashboard.roles.permissions', {roleId: this.permissionsForm.role_id})).then(response => {
                    if (!this.permissionsForm.hasErrors()) {
                        this.permissionsModal = false;
                        this.permissionsForm.role_id = null;
                        this.permissionsForm.name = null;
                        this.permissionsForm.permissions = [];
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>
