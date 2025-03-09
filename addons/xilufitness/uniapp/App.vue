<script>
	export default {
		onLaunch: function() {
			console.log('App Launch')
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		},
		globalData: {
			lat: '',
			lng: '',
			cityInfo: null,
			cityList: [],
			share_user_id: 0
		}
	}
</script>

<style lang="scss">
	@import "@/uni_modules/uview-ui/index.scss";
	@import '/static/css/global.css';

	/* #ifdef APP-PLUS  || H5*/
	.xilu_home_swiper .uni-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		background: #FFFFFF;
		opacity: 0.4;
	}

	.xilu_home_swiper .uni-swiper-dot-active {
		width: 26rpx;
		height: 8rpx;
		background: #FFCF00;
		border-radius: 4rpx;
		opacity: 1;
	}

	/deep/ .xilu_home_swiper .uni-swiper-dots {
		// 指示点整个区域
		bottom: 93rpx;
	}

	.xilu_course_swiper .uni-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		background: #FFFFFF;
		opacity: 0.4;
	}

	.xilu_course_swiper .uni-swiper-dot-active {
		width: 26rpx;
		height: 8rpx;
		background: #FFCF00;
		border-radius: 4rpx;
		opacity: 1;
	}

	/deep/ .xilu_course_swiper .uni-swiper-dots {
		// 指示点整个区域
		bottom: 30rpx;
	}

	/* #endif */
	/* #ifdef MP-WEIXIN */
	.xilu_home_swiper .wx-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		background: #FFFFFF;
		opacity: 0.4;
	}

	.xilu_home_swiper .wx-swiper-dot-active {
		width: 26rpx;
		height: 8rpx;
		background: #FFCF00;
		border-radius: 4rpx;
		opacity: 1;
	}

	.xilu_home_swiper .wx-swiper-dots.wx-swiper-dots-horizontal {

		bottom: 93rpx;
	}

	.xilu_course_swiper .wx-swiper-dot {
		width: 8rpx;
		height: 8rpx;
		background: #FFFFFF;
		opacity: 0.4;
	}

	.xilu_course_swiper .wx-swiper-dot-active {
		width: 26rpx;
		height: 8rpx;
		background: #FFCF00;
		border-radius: 4rpx;
		opacity: 1;
	}

	.xilu_course_swiper .wx-swiper-dots.wx-swiper-dots-horizontal {
		bottom: 30rpx;
	}

	/* #endif */
</style>