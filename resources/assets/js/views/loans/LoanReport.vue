<template>
<v-card-title>
    <v-card-title>
        <strong>Descargar reporte</strong>
        <v-btn
            small
            @click="download(report_selected)"
            :disabled="dialog"
            icon
        >
            <v-icon color="green">fa-file-excel-o</v-icon>
        </v-btn>
        <v-dialog
            v-model="dialog"
            hide-overlay
            persistent
            width="300"
        >
            <v-card
                color="primary"
                dark
            >
                <v-card-text>
                    Por favor espere
                    <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                    ></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-spacer></v-spacer>
        <v-select
            :items="reports"
            label="Tipo de reporte"
            item-text="name"
            item-value="id"
            v-model="report_selected"
        ></v-select>
    </v-card-title>
</card-title>
</template>
<script>
export default {
    data() {
        return {
            dialog: false,
            loading: true,
            reports: [
                {
                    name: 'Préstamos cancelados cuya suma de sus amortizaciones es distinto al monto desembolsado',
                    id: 1
                },{
                    name: 'Préstamos cancelados cuya suma de sus amortizaciones es mayor al monto desembolsado',
                    id: 2
                }, {
                    name: 'Préstamos con estado pendiente cuya suma de sus amortizaciones canceladas más sus amortizaciones con estado pendiente es igual a su saldo capital',
                    id: 3
                }
            ],
            report_selected: null
        }
    },
    methods: {
        download: function(report_id) {
            self = this
            self.dialog = true
            let parameters = {}
            if(report_id !== null && report_id !== 0) {
                axios({
                    url: `/api/pending_loan_report/${report_id}`,
                    method: 'GET',
                    responseType: 'blob'
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]))
                    const link = document.createElement('a')
                    link.href = url
                    link.setAttribute('download', 'Report Préstamos' + moment().format() + '.xls')
                    document.body.appendChild(link)
                    link.click()
                    window.URL.revokeObjectURL(url)
                    self.dialog = false
                })
            } else self.dialog = false
        }
    }
}
</script>