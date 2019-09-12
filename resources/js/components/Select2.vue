<template>
		<select class="form-control select2" :id='id' :multiple='multiple ? "multiple" : ""' :data-placeholder='placeholder' :disabled='disabled'
		        style="width: 100%;">
			<option :value='item.id' v-for='item in row'> {{item.name}} </option>
		</select>
		<!-- /.user-select-block -->
</template>

<script>
	   
    export default {
        name: "select2",
        props: {
			    id:String,
			    multiple:Boolean,
	        disabled:Boolean,
			    placeholder:String,
			    row:Array
        },
        data() {
            return {}
        },
        methods:{

        },
        mounted: function(){
            $('#'+this.id+'').select2();
        },
        watch:{
            // change_date: {
            //     immediate: true,    // 这句重要
            //     handler (val, oldVal) {
            //         if(oldVal !== undefined) {
            //
            //             console.log(val,oldVal);
            //         }
            //     }
            // }
        }
    }
</script>

<style scoped>

</style>
