<template>
    <SettingsMain active="repairTypes" title=" / علت های خرابی">
        <template #settings>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در علت خرابی"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-600 hover:bg-blue-500">
                                        جستجو
                                    </jet-button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <jet-button
                                        class="float-left"
                                        @click.native="newRepairTypes">
                                        ثبت علت خرابی جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        علت خرابی
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="repairType in repairTypes" :key="repairType.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{repairType.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(repairType.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{repairType.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editRepairTypes(repairType)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.settings.repairTypes.destroy',{repairTypeId:repairType.id})"
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

            <jet-confirmation-modal :show="submitRepairTypesModal" @close="submitRepairTypesModal = false">
                <template #title>
                    {{editRepairTypesModal ? 'ویرایش علت خرابی' : 'ثبت علت جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <input type="text"
                                   v-model="repairTypeForm.name"
                                   placeholder="علت خرابی"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="repairTypeForm.error('name')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <button v-on:click="repairTypeForm.status=1"
                                    :class="repairTypeForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="repairTypeForm.status=0"
                                    :class="repairTypeForm.status===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="repairTypeForm.error('status')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitRepairTypesModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editRepairTypesModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditRepairTypes"
                                :class="{ 'opacity-25': repairTypeForm.processing }"
                                :disabled="repairTypeForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewRepairTypes"
                                :class="{ 'opacity-25': repairTypeForm.processing }"
                                :disabled="repairTypeForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>

        </template>
    </SettingsMain>
</template>

<script>
    import SettingsMain from "@/Pages/Dashboard/Settings/SettingsMain";
    import JetButton from '@/Jetstream/Button'
    import JetInputError from '@/Jetstream/InputError';
    import {Inertia} from "@inertiajs/inertia";
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "RepairTypes",
        components: {SettingsMain, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            repairTypes: Array,
            searchQuery: String,
        },
        data() {
            return {
                query: '',
                submitRepairTypesModal: false,
                editRepairTypesModal: false,
                editRepairTypesId: '',
                repairTypeForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                    image: '',
                }, {
                    bag: 'repairTypeForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery
        },
        methods: {
            newRepairTypes() {
                this.editRepairTypesModal = false;
                this.repairTypeForm = this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                    image: '',
                }, {
                    bag: 'repairTypeForm',
                    resetOnSuccess: true
                });
                this.submitRepairTypesModal = true;
            },
            editRepairTypes(repairType) {
                this.repairTypeForm = this.$inertia.form({
                    '_method': 'PUT',
                    name: repairType.name,
                    status: repairType.status,
                    image: '',
                }, {
                    bag: 'repairTypeForm',
                    resetOnSuccess: true
                });
                this.editRepairTypesId = repairType.id;
                this.submitRepairTypesModal = true;
                this.editRepairTypesModal = true;
            },
            submitNewRepairTypes() {
                this.repairTypeForm.post(route('dashboard.settings.repairTypes.store')).then(response => {
                    if (!this.repairTypeForm.hasErrors()) {
                        this.submitRepairTypesModal = false;
                    }
                })
            },
            submitEditRepairTypes() {
                this.repairTypeForm.repairTypes = this.repairTypeList;
                this.repairTypeForm.post(route('dashboard.settings.repairTypes.update', {repairTypeId: this.editRepairTypesId})).then(response => {
                    if (!this.repairTypeForm.hasErrors()) {
                        this.submitRepairTypesModal = false;
                        this.editRepairTypesModal = false;
                    }
                })
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.settings.repairTypes.list'), {
                    method: 'GET',
                    data: {
                        query: this.query
                    },
                })
            },
            statusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 0:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
