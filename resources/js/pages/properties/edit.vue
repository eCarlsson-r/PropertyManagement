<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppContent from '@/components/AppContent.vue';
import type { Location } from '@/components/properties/PropertyForm.vue';
import { dashboard } from '@/routes';
import { index as propertiesIndex, update as propertiesUpdate } from '@/routes/properties';

interface Property {
    id: number;
    name: string;
    currency: string;
    mobile: string;
    location_id: number;
    notes: string;
    location?: Location;
}

const props = defineProps<{
    property: Property;
    locations: Location[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Properties',
                href: propertiesIndex(),
            },
            {
                title: 'Edit Property',
            },
        ],
    },
});

const form = useForm({
    name: props.property.name,
    currency: props.property.currency,
    mobile: props.property.mobile,
    location_id: String(props.property.location_id),
    notes: props.property.notes,
});

function submit() {
    form.put(propertiesUpdate.url(props.property.id));
}
</script>

<template>

    <Head title="Edit Property" />

    <AppContent>
        <form id="update_property">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Properti</h6>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <input type="number" name="property-code" class="d-none" />
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-home"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="property-name" name="property-name"
                                    placeholder="Nama Properti" />
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                                <input type="text" class="d-none" id="property-location" name="property-location" />
                                <label class="form-control" id="property-location-value"
                                    data-i18n="location">Lokasi</label>
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="property-manager" name="property-manager"
                                    placeholder="Pengelola Properti" />
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div class="input-group-text">
                                        <i class="fi fi-id"></i>
                                    </div>
                                    <span class="input-group-text">+62</span>
                                </div>
                                <input type="tel" class="form-control" id="property-phone" name="property-phone"
                                    placeholder="No. WA Pengelola" />
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-money-bill"></i>
                                    </div>
                                </div>
                                <select class="form-control selectpicker" id="property-currency"
                                    name="property-currency">
                                    <option selected disabled hidden>Mata Uang</option>
                                    <option value="IDR">Indonesian Rupiah (IDR)</option>
                                </select>
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Nama Pemilik Rekening</div>
                                </div>
                                <input type="text" class="form-control" id="property-account-owner"
                                    name="property-account-owner" placeholder="Nama Pemilik Rekening" />
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Pilih Bank</div>
                                </div>
                                <input type="text" class="form-control" id="property-account-bank"
                                    name="property-account-bank" placeholder="Pilih Bank" />
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Nomor Rekening</div>
                                </div>
                                <input type="text" class="form-control" id="property-account-number"
                                    name="property-account-number" placeholder="Nomor Rekening" />
                            </div>

                            <button class="btn btn-primary" id="updatePropertyBtn">Simpan</button>
                            <button class="btn btn-danger" id="removePropertyBtn">Hapus Properti</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Peraturan Properti</h6>
                            <div class="dropdown no-arrow">
                                <a id="add-rule-btn">
                                    <span class="fa fa-plus"></span>
                                </a>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="no-data">Tidak Ada Data</div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Unit/Kamar</h6>
                            <div class="dropdown no-arrow">
                                <a id="add-room-btn">
                                    <span class="fa fa-plus"></span>
                                </a>
                            </div>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-home"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="room-name" name="room-name"
                                    data-i18n="[placeholder]room-name" />
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-list-ul"></i>
                                    </div>
                                </div>
                                <select class="form-control unitSelector" id="room-type" name="room-type"
                                    data-i18n="[placeholder]room-type"></select>
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-level-up-alt"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="room-floor" name="room-floor"
                                    data-i18n="[placeholder]room-floor" />
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-room" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th data-field="room-name">Name</th>
                                            <th data-field="room-type" data-formatter="propertyUnitFormatter">Name</th>
                                            <th data-field="room-floor">Floor</th>
                                            <th data-events="roomActionHandler" data-formatter="roomActionFormatter">
                                                Contact Person</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </AppContent>
</template>