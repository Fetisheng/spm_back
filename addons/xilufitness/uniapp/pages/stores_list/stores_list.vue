<template>
	<view class="xilu">
		<view class="container">
			<view class="page-head bg-normal ptb30 plr25">
				<view class="xilu_search_nav flex-box">
					<image src="@/static/images/xilu_icon_address_yellow.png" mode="widthFix" class="ico26"></image>
					<picker mode="selector" :value="c_index" :range="city_list" range-key="name" @change="changeCity">
						<view class="fs28 fw500 colf plr10">{{city_name}}</view>
					</picker>
					<image src="@/static/images/xilu_icon_arrowdown_white.png" mode="widthFix"
						class="xilu_search_nav_arrow"></image>
					<view class="xilu_sq mlr20"></view>
					<image src="@/static/images/xilu_icon_search.png" mode="aspectFill" class="ico28"></image>
					<input type="text" v-model="keywords" placeholder="请输入" class="flex-grow-1 plr10 fs26 colf"
						placeholder-class="col9" />
					<view @click="searchShop()" class="xilu_search_btn">搜索</view>
				</view>

			</view>
			<view class="page-body plr25 pb30" style="padding-top: 145rpx;">

				<template v-if="list.length > 0">
					<view class="xilu_item" v-for="(vo,index) in list" @click.stop="shop_detail(vo.id)">
						<image :src="vo.xilufitness_urls.shop_image" mode="aspectFill" class="xilu_item_cover">
						</image>
						<view class="pl20 ptb20 pr30">
							<view class="m-ellipsis fs30 fw500 colf lh42">{{vo.shop_name || ''}}</view>
							<view class="flex-box flex-between mt10">
								<view class="m-ellipsis fs28 col9 w512">{{vo.address || ''}}</view>
								<view class="col2 fs24">
									<distance-format :distance="vo.distance"></distance-format>
								</view>
							</view>
						</view>
					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无门店数据'" :lineHeight="500"></empty-data>
				</template>

			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				list: [],
				page: 1,
				total_count: 0,
				city_id: 0,
				keywords: '',
				city_name: '',
				city_list: [],
				c_index: ''
			}
		},
		methods: {
			//选择城市
			changeCity(e) {
				let city_list = this.city_list;
				let c_index = e.detail.value || '';
				this.c_index = c_index;
				console.log('cityInfo', city_list[c_index]);
				if (city_list[c_index]) {
					this.city_name = city_list[c_index]['name'] || this.city_name;
					this.city_id = city_list[c_index]['id'] || _this.city_id;
					app.globalData.cityInfo = city_list[c_index];
					this.page = 1;
					this.list = [];
					this.total_count = 0;
					this.getShopList();
				}
			},
			/**
			 * 获取城市列表
			 */
			getCityList() {
				let _this = this;
				if (app.globalData.cityList.length > 0) {
					this.city_list = app.globalData.cityList;
					return false;
				} else {
					this.$http({
						url: '/addons/xilufitness/home/getCityList',
						data: {
							pid: 0
						},
						method: 'get'
					}).then(res => {
						if (res.code == 1) {
							_this.city_list = res.data.list || [];
							app.globalData.cityList = res.data.list || [];
						}
					}).catch(error => {
						console.log('cityListError', error);
					})
				}

			},
			//获取门店列表
			getShopList() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/shop/index',
					data: {
						page: _this.page,
						lat: app.globalData.lat,
						lng: app.globalData.lng,
						city_id: _this.city_id,
						keywords: _this.keywords
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count;
					}
				}).catch(error => {
					console.log('shopListError', error);
				});
			},
			//搜索门店
			searchShop() {
				this.clearData();
				this.getShopList();
			},
			//清楚数据
			clearData() {
				this.page = 1;
				this.list = [];
				this.total_count = 0;
			},
			//门店详情
			shop_detail(id) {
				this.$api.navigate('../stores_info/stores_info?id=' + id)
			}
		},
		onLoad() {
			this.city_id = app.globalData.cityInfo.id || 0;
			this.city_name = app.globalData.cityInfo.name || '';
			this.getShopList();
			this.getCityList();
		},
		onReachBottom() {
			let list = this.list;
			let total_count = this.total_count;
			if (total_count > list.length) {
				this.page = this.page + 1;
				this.getShopList();
			}
		},
		onShareAppMessage() {

		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_search_nav {
			width: 100%;
			height: 85rpx;
			background: #404243;
			border-radius: 43rpx;
			padding-left: 25rpx;
			padding-right: 5rpx;

			&_arrow {
				width: 17rpx;
			}
		}

		&_sq {
			width: 1px;
			height: 24rpx;
			background: #666666;
		}

		&_search_btn {
			width: 143rpx;
			height: 75rpx;
			line-height: 75rpx;
			text-align: center;
			font-size: 26rpx;
			font-weight: 400;
			color: #0E0E0F;
			background: #FFCF00;
			border-radius: 43rpx;
		}

		&_item {
			width: 700rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-bottom: 20rpx;

			&_cover {
				width: 100%;
				height: 325rpx;
				display: block;
				border-radius: 20rpx 20rpx 0 0;
			}
		}
	}

	.w512 {
		width: 512rpx;
	}
</style>