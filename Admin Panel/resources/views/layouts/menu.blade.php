@php
    $user = Auth::user();
    if(Auth::guard('admin')->check()){
        $user = Auth::guard('admin')->user();
    } elseif(Auth::guard('expert')->check()){
        $user = Auth::guard('expert')->user();
    }
@endphp

<nav class="sidebar-nav">

    <ul id="sidebarnav">
        <li>
            <a class="waves-effect waves-dark" href="{!! route('admin.dashboard') !!}" aria-expanded="false">
                <i class="mdi mdi-home"></i>
                <span class="hide-menu">{{trans('lang.dashboard')}}</span>
            </a>
        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! route('admin.map') !!}" aria-expanded="false">
                <i class="mdi mdi-home-map-marker"></i>
                <span class="hide-menu">{{trans('lang.god_eye')}}</span>
            </a>
        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! route('admin.zone') !!}" aria-expanded="false">
                <i class="mdi mdi-map-marker-circle"></i>
                <span class="hide-menu">{{trans('lang.zone')}}</span>
            </a>
        </li>

        <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-lock-outline"></i>
                <span class="hide-menu">{{trans('lang.access_control')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.role.index') !!}">{{trans('lang.role_plural')}}</a></li>
                <li><a href="{!! route('admin.admin.users') !!}">{{trans('lang.admin_plural')}}</a></li>
            </ul>
        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! route('admin.users') !!}" aria-expanded="false">
                <i class="mdi mdi-account-multiple"></i>
                <span class="hide-menu">{{trans('lang.user_customer')}}</span>
            </a>
        </li>

        <li>
            <a class="has-arrow waves-effect waves-dark driver_menu" href="#" aria-expanded="false">
                <i class="mdi mdi-account-card-details"></i>
                <span class="hide-menu">{{trans('lang.owner_vendor')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse driver_sub_menu">
                <li class="all_driver_menu"><a href="{!! route('admin.vendors') !!}">{{trans('lang.all_vendors')}}</a></li>
                <li class="approve_driver_menu"><a href="{!! route('admin.vendors.approved') !!}">{{trans('lang.approved_vendors')}}</a></li>
                <li class="pending_driver_menu"><a href="{!! route('admin.vendors.pending') !!}">{{trans('lang.approval_pending_vendors')}}</a></li>
            </ul>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.stores') !!}" aria-expanded="false">
                <i class="mdi mdi-shopping"></i>
                <span class="hide-menu">{{trans('lang.store_plural')}}</span>
            </a>
        </li>

        <li>
            <a class="has-arrow waves-effect waves-dark driver_menu" href="#" aria-expanded="false">
                <i class="mdi mdi-account-card-details"></i>
                <span class="hide-menu">{{trans('lang.driver_plural')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse driver_sub_menu">
                <li class="all_driver_menu"><a href="{!! route('admin.drivers') !!}">{{trans('lang.all_drivers')}}</a></li>
                <li class="approve_driver_menu"><a href="{!! route('admin.drivers.approved') !!}">{{trans('lang.approved_drivers')}}</a></li>
                <li class="pending_driver_menu"><a href="{!! route('admin.drivers.pending') !!}">{{trans('lang.approval_pending_drivers')}}</a></li>
            </ul>
        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! route('admin.appointments.index') !!}" aria-expanded="false">
                <i class="mdi mdi-calendar-clock"></i>
                <span class="hide-menu">{{trans('lang.appointment_plural', ['default' => 'Appointments'])}}</span>
            </a>
        </li>

        <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-calendar-check"></i>
                <span class="hide-menu">{{trans('lang.report_plural')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.report.index', ['type' => 'sales']) !!}">{{trans('lang.reports_sale')}}</a></li>
            </ul>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.categories') !!}" aria-expanded="false">
                <i class="mdi mdi-clipboard-text"></i>
                <span class="hide-menu">{{trans('lang.category_plural')}}</span>
            </a>
        </li>

        <li>
            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-cart"></i>
                <span class="hide-menu">{{trans('lang.item_plural')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.products.index') !!}">{{trans('lang.item_plural')}}</a></li>
                <li><a href="{!! route('admin.stock.index') !!}">{{trans('lang.stock_tracking', ['default' => 'Stock Tracking'])}}</a></li>
            </ul>
        </li>

        <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-plus-box"></i>
                <span class="hide-menu">{{trans('lang.attribute_plural')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.attributes') !!}">{{trans('lang.item_attribute_id')}}</a></li>
                <li><a href="{!! route('admin.reviewattributes') !!}">{{trans('lang.review_attribute_plural')}}</a></li>
            </ul>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.orders.index') !!}" aria-expanded="false">
                <i class="mdi mdi-library-books"></i>
                <span class="hide-menu">{{trans('lang.order_plural')}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.returns.index') !!}" aria-expanded="false">
                <i class="mdi mdi-keyboard-return"></i>
                <span class="hide-menu">{{trans('lang.returns_refunds', ['default' => 'Returns & Refunds'])}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.orders.index') !!}" aria-expanded="false">
                <i class="mdi mdi-star"></i>
                <span class="hide-menu">{{trans('lang.product_reviews', ['default' => 'Product Reviews'])}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.gift-card.index') !!}" aria-expanded="false">
                <i class="mdi mdi-wallet-giftcard"></i>
                <span class="hide-menu">{{trans('lang.gift_card_plural')}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.coupons') !!}" aria-expanded="false">
                <i class="mdi mdi-sale"></i>
                <span class="hide-menu">{{trans('lang.coupon_plural')}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.documents') !!}" aria-expanded="false">
                <i class="mdi mdi-file-document"></i>
                <span class="hide-menu">{{trans('lang.document_plural')}}</span>
            </a>
        </li>

        <li>
            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-table"></i>
                <span class="hide-menu">{{trans('lang.notification_plural')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.notification') !!}">{{trans('lang.general_notification')}}</a></li>
                <li><a href="{!! route('admin.dynamic-notification.index') !!}">{{trans('lang.dynamic_notification')}}</a></li>
            </ul>
        </li>

        <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-bank"></i>
                <span class="hide-menu">{{trans('lang.payment_plural')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.payments') !!}">{{trans('lang.store_payments')}}</a></li>
                <li><a href="{!! route('admin.storesPayouts') !!}">{{trans('lang.stores_payout_plural')}}</a></li>
                <li><a href="{!! route('admin.driver.driverpayments') !!}">{{trans('lang.driver_plural')}} {{trans('lang.payment_plural')}}</a></li>
                <li><a href="{!! route('admin.driversPayouts') !!}">{{trans('lang.drivers_payout')}}</a></li>
                <li><a href="{!! route('admin.walletstransaction') !!}">{{trans('lang.wallet_transaction')}}</a></li>
                <li><a href="{!! route('admin.payoutRequests.stores') !!}">{{trans('lang.payout_request')}}</a></li>
            </ul>
        </li>

        <li>
            <a class="waves-effect waves-dark" href="{!! route('admin.setting.banners') !!}" aria-expanded="false">
                <i class="mdi mdi-monitor-multiple "></i>
                <span class="hide-menu">{{trans('lang.menu_items')}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.cms') !!}" aria-expanded="false">
                <i class="mdi mdi-book-open-page-variant"></i>
                <span class="hide-menu">{{trans('lang.cms_plural')}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark onboard_menu" href="{!! route('admin.on-board') !!}" aria-expanded="false">
                <i class="mdi mdi-cellphone"></i>
                <span class="hide-menu">{{trans('lang.on_board_plural')}}</span>
            </a>
        </li>

        <li><a class="waves-effect waves-dark" href="{!! route('admin.email-templates.index') !!}" aria-expanded="false">
                <i class="mdi mdi-email"></i>
                <span class="hide-menu">{{trans('lang.email_templates')}}</span>
            </a>
        </li>

        <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                <i class="mdi mdi-settings"></i>
                <span class="hide-menu">{{trans('lang.app_setting')}}</span>
            </a>
            <ul aria-expanded="false" class="collapse">
                <li><a href="{!! route('admin.settings.app.globals') !!}">{{trans('lang.app_setting_globals')}}</a></li>
                <li><a href="{!! route('admin.currencies') !!}">{{trans('lang.currency_plural')}}</a></li>
                <li><a href="{!! route('admin.payment.stripe') !!}">{{trans('lang.payment_methods')}}</a></li>
                <li><a href="{!! route('admin.settings.app.adminCommission') !!}">{{trans('lang.store_admin_commission')}}</a></li>
                <li><a href="{!! route('admin.settings.app.radiusConfiguration') !!}">{{trans('lang.radios_configuration')}}</a></li>
                <li><a href="{!! route('admin.tax') !!}">{{trans('lang.vat_setting')}}</a></li>
                <li><a href="{!! route('admin.settings.app.deliveryCharge') !!}">{{trans('lang.deliveryCharge')}}</a></li>
                <li><a href="{!! route('admin.settings.app.documentVerification') !!}">{{trans('lang.document_verification')}}</a></li>
                <li><a href="{!! route('admin.settings.app.languages') !!}">{{trans('lang.languages')}}</a></li>
                <li><a href="{!! route('admin.setting.specialOffer') !!}">{{trans('lang.special_offer')}}</a></li>
                <li><a href="{!! route('admin.termsAndConditions') !!}">{{trans('lang.terms_and_conditions')}}</a></li>
                <li><a href="{!! route('admin.privacyPolicy') !!}">{{trans('lang.privacy_policy')}}</a></li>
            </ul>
        </li>
    </ul>

    <p class="web_version"></p>

</nav>