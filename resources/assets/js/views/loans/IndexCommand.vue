<template>
 <v-card class="elevation-12">
    <v-card-title>
      Prestamos Comando 
      <v-btn small color="success" @click="download" 
        :disabled="dialog"
        :loading="dialog"
        ><v-icon>file_download</v-icon>Excel
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
      <v-text-field
        v-model="search"
        append-icon="search"
        label="Buscar"
        single-line
        hide-details
      ></v-text-field>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="loans"
      :search="search"
      :loading="loading"
      :pagination.sync="pagination"
      :total-items="totalLoans"
    >
      <template slot="items" slot-scope="props">
        <td class="text-xs-left">{{ props.item.PresNumero }}</td>
        <td class="text-xs-left">{{ props.item.PresFechaDesembolso }}</td>
        <td class="text-xs-left">{{ props.item.PresCuotaMensual }}</td>
        <td class="text-xs-left">{{ props.item.PresSaldoAct }}</td>
        <td class="text-xs-left">{{ props.item.PadMatricula }}</td>
        <td class="text-xs-left">{{ props.item.PadCedulaIdentidad }}</td>
        <td class="text-xs-left">{{ props.item.PadNombres }}</td>
        <td class="text-xs-left">{{ props.item.PadNombres2do }}</td>
        <td class="text-xs-left">{{ props.item.PadPaterno }}</td>
        <td class="text-xs-left">{{ props.item.PadMaterno }}</td>
        <td class="text-xs-left">{{ props.item.State }}</td>
        <td class="text-xs-left">{{ props.item.City }}</td>
        <td class="text-xs-left">{{ props.item.Discount }}</td>
        <td class="text-xs-left"> <a  v-bind:href="generate_link(props.item.IdPrestamo)"><v-icon>assignment</v-icon></a> </td>
      </template>
      <v-alert slot="no-results" :value="true" color="error" icon="warning">
        Su busqueda para "{{ search }}" no se encontraron resultados.
      </v-alert>
    </v-data-table>
  </v-card>
</template>
<script>
export default {
    data() {
            return {
            loans: [],
            search: '',
            headers: [
              { text: 'Numero de Prestamo', value: 'PresNumero' },
              { text: 'Fecha Desembolso', value: 'PresFechaDesembolso' },
              { text: 'Cuota', value: 'PresCuotaMensual' },
              { text: 'Saldo', value: 'PresSaldoAct' },
              { text: 'Matricula', value: 'PadMatricula' },
              { text: 'CI ', value: 'PadCedulaIdentidad' },
              { text: '1er Nombre', value: 'PadNombres' },
              { text: '2do Nombre ', value: 'PadNombres2do' },
              { text: 'Paterno ', value: 'PadPaterno' },
              { text: 'Materno ', value: 'PadMaterno' },
              { text: 'Frecuencia ', value: 'State' },
              { text: 'Departamento', value: 'City' },
              { text: 'Descuento ', value: 'Discount' },
              { text: 'Accion ' }
            ],
            dialog: false,
            loading: true,
            pagination: {},
            totalLoans: 0,
            }
    },
    created(){
          
          
  
    },
    watch: {
      pagination: {
        handler () {
          this.getDataFromApi()
            .then(data => {
              this.loans = data.items
              this.totalLoans = data.total
            })
        },
        deep: true
      },
      dialog (val) {
        if (!val) return

        //setTimeout(() => (this.dialog = false), 4000)
      }
    },
    mounted () {
        this.getLoans()
            .then(
                    this.getDataFromApi()
                        .then(data => {
                        this.loans = data.items
                        this.totalLoans = data.total
                        })
                
            );
    },
    // define methods under the `methods` object
    methods: {
      generate_link(id){
          return 'http://sismu.muserpol.gob.bo/musepol/akardex.aspx?'+id;
        //console.log(this.loans)
      },
       download: function (event) {
        // `this` inside methods point to the Vue instance
        self = this;
        self.dialog = true
        //  self.dialog = true;
        axios({
            url: '/api/loans_command_report',
            method: 'GET',
            responseType: 'blob', // important
          }).then((response) => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Prestamos Altas Comando.xls');
            document.body.appendChild(link);
            link.click();
              self.dialog = false;
          });
      },
      getDataFromApi () {
        
        return new Promise((resolve, reject) => {
          const { sortBy, descending, page, rowsPerPage } = this.pagination

          let items = this.loans
          const total = items.length

          if (this.pagination.sortBy) {
            items = items.sort((a, b) => {
              const sortA = a[sortBy]
              const sortB = b[sortBy]

              if (descending) {
                if (sortA < sortB) return 1
                if (sortA > sortB) return -1
                return 0
              } else {
                if (sortA < sortB) return -1
                if (sortA > sortB) return 1
                return 0
              }
            })
          }

          if (rowsPerPage > 0) {
            items = items.slice((page - 1) * rowsPerPage, page * rowsPerPage)
          }

        //   setTimeout(() => {
           // this.loading = false
            resolve({
              items,
              total
            })
        //   }, 1000)
        })
      },
      getLoans(){
          this.loading = true;
          console.log(this.loading)
          return new Promise((resolve,reject)=>{
            axios
           .get('/api/loans_command')
           .then((response)=>{
                //this.data.loans = response.data;
                console.log('obteniendo lista ')
                this.loans = response.data;
                this.loading = false;
                resolve();
                // console.log(self.loans);
            });
          });
           
      }
    
    },
   
}
</script>
