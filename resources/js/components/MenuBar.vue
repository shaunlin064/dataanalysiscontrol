<template>
	<li v-bind:class="{ 'treeview': item.children && Object.keys(item.children).length > 0}">
		<a :href="item.href ? item.href : '/#'">
			<i v-bind:class=item.iconClassName></i> <span>{{item.title}}</span>
			<span class='pull-right-container'>
				<i v-if='item.children && Object.keys(item.children).length > 0' class='fa fa-angle-left pull-right'></i>
        <small v-else-if="item.label" class='label pull-right'  v-bind:class="{ 'bg-green': item.label.color }">{{item.label.name}}</small>
			</span>
		</a>
		<ul v-if='item.children' class='treeview-menu'>
			<li  :class="{'treeview':children.children && Object.keys(children.children).length > 0 }" v-for="children in item.children">
				<a :href="children.href ? (children.title == '個人檢視' ? children.href+user_id :  children.href ) : '/#'">
					<i :class=children.iconClassName></i>{{children.title}}
					<span v-if='children.children' class='pull-right-container'>
            <i class='fa fa-angle-left pull-right'></i>
          </span>
				</a>
				<ul v-if='children.children' class='treeview-menu'>
					<menu-component :item='items' v-for='items in children.children' :key="children.children.title"></menu-component>
				</ul>
			</li>
		</ul>
	</li>
</template>

<script>
    export default {
        props: {
            item : Object,
		        user_id: Number
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
