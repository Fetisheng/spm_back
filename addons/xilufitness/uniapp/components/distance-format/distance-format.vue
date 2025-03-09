<template>
	<text>{{distanceFormat(distance)}}</text>
</template>

<script>
	export default {
		name:"distance-format",
		data() {
			return {
				
			};
		},
		props:{
			distance:[Number]
		},
		computed:{
			//格式化距离显示
			distanceFormat(){
				return (distance) => {
					if (parseFloat(distance) > 1000) {
						return (parseFloat(distance) / 1000).toFixed(0) + 'km'
					} else {
						return (parseFloat(distance).toFixed(0)) + 'm'
					}
				}
			}
		}
	}
</script>

<style>

</style>