import {render} from "react-dom";
import React from "react";
import {NoticeBlack} from '@wpmudev/shared-notifications-black-friday';

const {__} = wp.i18n;
const {createInterpolateElement} = wp.element;
const $ = jQuery;

class BlackFriday extends React.Component {
	render() {
		if (!this.isWithinTimeFrame()) {
			return <div/>;
		}

		const buildType = _black_friday.build_type;
		const utmSource = buildType === 'full'
			? 'branda_pro'
			: 'branda_free';
		const link = 'https://wpmudev.com/black-friday/?coupon=BFP-2021&utm_source=' + utmSource + '&utm_medium=referral&utm_campaign=bf2021';

		return <NoticeBlack
			link={link}
			sourceLang={{
				discount: __('50% Off', 'wds'),
				closeLabel: __('Close', 'wds'),
				linkLabel: __('See the deal', 'wds')
			}}
			onCloseClick={() => this.dismissBFNotice()}
		>
			<p>{createInterpolateElement(
				__('<strong>Black Friday Offer!</strong> Get 11 Pro plugins on unlimited sites and much more with 50% OFF WPMU DEV Agency plan FOREVER.'),
				{strong: <strong style={{color: "#FFF"}}/>}
			)}</p>
			<p><small>{__('* Only admin users can see this message', 'wds')}</small></p>
		</NoticeBlack>
	}

	isWithinTimeFrame() {
		const date = new Date();
		if (date.getFullYear() !== 2021) {
			return false;
		}

		const isNovember = date.getMonth() === 10;
		const isDecember = date.getMonth() === 11;

		return isNovember || (isDecember && date.getDate() < 6);
	}

	dismissBFNotice() {
		$.post(ajaxurl, {action: 'branda_dismiss_black_friday_notice'});
	}
}

wp.domReady(() => {
	const blackFriday = document.getElementById('branda-black-friday-2021');
	if (blackFriday) {
		render(<BlackFriday/>, blackFriday);
	}
});
