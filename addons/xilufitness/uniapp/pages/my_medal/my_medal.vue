<template>
	<view class="xilu">
		<view class="container">
			<view class="p30">
				<view @click="show_media_rules()" class="flex-box pb20">
					<image src="@/static/images/xilu_icon20.png" mode="widthFix" class="ico32"></image>
					<view class="pl10 col2 fs28 lh34">勋章获取规则</view>
				</view>
				<view>
					<template v-if="list.length > 0">
						<view class="xilu_medal_item" v-for="(vo,keys) in list">
							<image @click="show_success(vo)" v-if="vo.is_active == 1"
								:src="vo.xilufitness_urls.thumb_active_image" mode="aspectFill"
								class="xilu_medal_item_img"></image>
							<image @click="show_rule(vo)" v-else :src="vo.xilufitness_urls.thumb_image"
								mode="aspectFill" class="xilu_medal_item_img"></image>
							<view class="lh48">{{vo.medal_name || ''}}</view>
						</view>
					</template>

					<template v-else>
						<empty-data :tips="'暂无勋章'" :lineHeight="$children00"></empty-data>
					</template>
				</view>
			</view>
		</view>
		<u-popup :show="show" mode="center" bgColor="transparent" @close="close" @open="open">
			<view class="xilu_popup1">
				<image :src="show_image" mode="aspectFill"></image>
				<view class="xilu_pop_btn">恭喜你解锁新勋章！</view>
			</view>
		</u-popup>
		<u-popup :show="showinfo" mode="center" bgColor="transparent" @close="infoclose" @open="infoopen">
			<view class="xilu_popup2">
				<view class="fs34 col9 lh40 tc">勋章说明详情</view>
				<image src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close" @click="infoclose">
				</image>
				<image :src="show_image" mode="aspectFill" class="xilu_popup2_cover"></image>
				<view class="xilu_popup2_content">
					<template v-for="(vo,keys) in description">
						<view>{{keys + 1}}.{{vo}}；</view>
					</template>
				</view>
			</view>
		</u-popup>
		<u-popup :show="showrules" mode="center" bgColor="transparent" @close="rulesclose" @open="rulesopen">
			<view class="xilu_popup2">
				<view class="fs34 col9 lh40 tc">勋章获取规则</view>
				<image src="@/static/images/xilu_close.png" mode="aspectFill" class="xilu_close" @click="rulesclose">
				</image>
				<view class="xilu_popup2_content" style="margin-top: 40rpx;">
					<mp-html :content="tips_content"></mp-html>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {
				show: false,
				showinfo: false,
				showrules: false,
				list: [],
				page: 1,
				total_count: 0,
				description: [],
				show_image: '',
				tips_content: ''
			}
		},
		methods: {
			close() {
				this.show = false
			},
			open() {
				this.show = true
			},
			infoclose() {
				this.showinfo = false
			},
			infoopen() {
				this.showinfo = true
			},
			rulesclose() {
				this.showrules = false
			},
			rulesopen() {
				this.showrules = true
			},
			show_media_rules() {
				this.showrules = true;
			},
			show_rule(media) {
				this.description = media.description || [];
				this.showinfo = true;
				this.show_image = media.xilufitness_urls.thumb_image;
			},
			show_success(media) {
				this.show_image = media.xilufitness_urls.thumb_active_image;
				this.show = true
			},
			//获取勋章
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/getMyMedia',
					data: {
						page: _this.page
					},
					method: 'GET'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count;
					}
				})
			},
			//勋章规则
			getCmsDetail() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/cms/detail',
					data: {
						is_type: 7
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.tips_content = (res.data.info && res.data.info.content) ? res.data.info.content : '';
					}
				}).catch(error => {
					console.log('cmsError', error);
				})
			}
		},
		onLoad() {
			this.getLists();
			this.getCmsDetail();
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getLists();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_medal_item {
			display: inline-block;
			vertical-align: top;
			width: 140rpx;
			font-size: 34rpx;
			font-weight: 400;
			color: #FFFFFF;
			margin-right: 40rpx;
			text-align: center;
			margin-top: 30rpx;

			&_img {
				width: 120rpx;
				height: 120rpx;
				display: block;
				margin-left: auto;
				margin-right: auto;
				margin-bottom: 20rpx;
			}
		}

		&_medal_item:nth-of-type(4n) {
			margin-right: 0;
		}

		&_popup1 {
			width: 540rpx;

			image {
				width: 540rpx;
				height: 540rpx;
				display: block;
				margin-left: auto;
				margin-right: auto;
				margin-bottom: 20rpx;
			}
		}

		&_pop_btn {
			width: 376rpx;
			height: 76rpx;
			line-height: 76rpx;
			background: #FFF45A;
			border-radius: 50rpx;
			margin-top: 20rpx;
			font-size: 34rpx;
			font-weight: 400;
			color: #333333;
			text-align: center;
			margin-left: auto;
			margin-right: auto;
		}

		&_popup2 {
			width: 700rpx;
			background: #404243;
			border-radius: 20rpx;
			border: 1px solid #454545;
			position: relative;
			padding: 30rpx 20rpx 30rpx;

			&_cover {
				width: 314rpx;
				height: 314rpx;
				display: block;
				margin-left: auto;
				margin-right: auto;
			}

			&_content {
				margin-top: 6rpx;
				font-size: 28rpx;
				font-weight: 400;
				color: #FFFFFF;
				line-height: 40rpx;
				white-space: pre-line;
			}
		}

		&_close {
			width: 30rpx;
			height: 30rpx;
			top: 35rpx;
			right: 40rpx;
			position: absolute;
		}

	}
</style>