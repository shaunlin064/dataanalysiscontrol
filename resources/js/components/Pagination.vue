<template>
	<ul class="pagination" role="navigation" v-if='arg.hasPages'>
		<li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')" v-if='arg.onFirstPage'>
			<span class="page-link" aria-hidden="true">&lsaquo;</span>
		</li>
		<li class="page-item" v-else>
			<a class="page-link" :href='arg.previousPageUrl' rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
		</li>
		
		<!--{{ Pagination Elements }}-->
		
		<!--{{ "Three Dots" Separator }}-->
		
		<!--<li class="page-item disabled" aria-disabled="true" v-for='(element,key) in arg.element' v-if='element == null'><span class="page-link">...</span></li>-->
		<!--{{ Array Of Links }}-->
				<!--<li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>-->
				<li class="page-item" v-for='(element,key) in arg.element' v-if='element !== null'><a class="page-link" :href='element'>{{ getCurrentPage(element) }}</a></li>
		<!--{{ Next Page Link }}-->
		<li class="page-item" v-if='arg.hasMorePages'>
			<a class="page-link" :href='arg.nextPageUrl' rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
		</li>
		<li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')" v-else>
			<span class="page-link" aria-hidden="true">&rsaquo;</span>
		</li>
	</ul>
</template>

<script>
    export default {
        name: "Pagination",
        props:{
            arg : Object,
        },
        data() {
            return {
            
            }
            
        },
        methods: {
            getCurrentPage(url){
                let URLParams = new URL(url).searchParams;

                let data = {};
                for (let [key, value] of URLParams.entries()) {
                    
                    data[`${key}`] = value;
                }
                return data['page'];
            }
        },
        mounted() {}
    }
</script>

<style scoped>

</style>
