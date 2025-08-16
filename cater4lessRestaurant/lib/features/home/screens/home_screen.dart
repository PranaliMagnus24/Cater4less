import 'package:flutter/cupertino.dart';
import 'package:stackfood_multivendor_restaurant/common/controllers/theme_controller.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/confirmation_dialog_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/order_shimmer_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/order_widget.dart';
import 'package:stackfood_multivendor_restaurant/features/auth/controllers/auth_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/home/widgets/ads_section_widget.dart';
import 'package:stackfood_multivendor_restaurant/features/home/widgets/order_summary_card.dart';
import 'package:stackfood_multivendor_restaurant/features/notification/controllers/notification_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/order/controllers/order_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/order/domain/models/order_model.dart';
import 'package:stackfood_multivendor_restaurant/features/home/widgets/order_button_widget.dart';
import 'package:stackfood_multivendor_restaurant/features/profile/controllers/profile_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/subscription/controllers/subscription_controller.dart';
import 'package:stackfood_multivendor_restaurant/helper/route_helper.dart';
import 'package:stackfood_multivendor_restaurant/util/dimensions.dart';
import 'package:stackfood_multivendor_restaurant/util/images.dart';
import 'package:stackfood_multivendor_restaurant/util/styles.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:shimmer_animation/shimmer_animation.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    await Get.find<ProfileController>().getProfile();
    await Get.find<OrderController>().getCurrentOrders();
    await Get.find<NotificationController>().getNotificationList();
  }

  final WidgetStateProperty<Icon?> thumbIcon = WidgetStateProperty.resolveWith<Icon?>(
     (Set<WidgetState> states) {
      if (states.contains(WidgetState.selected)) {
        return Icon(Icons.circle, color: Get.find<ThemeController>().darkTheme ? Colors.black : Colors.white);
      }
      return Icon(Icons.circle, color: Get.find<ThemeController>().darkTheme ? Colors.white: Colors.black);
    },
  );

  @override
  Widget build(BuildContext context) {
    return Scaffold(

      appBar: AppBar(
        backgroundColor: Theme.of(context).cardColor,
        leading: Padding(
          padding: const EdgeInsets.all(Dimensions.paddingSizeSmall),
          child: Image.asset(Images.logo, height: 30, width: 30),
        ),
        titleSpacing: 0,
        surfaceTintColor: Theme.of(context).cardColor,
        shadowColor: Theme.of(context).disabledColor.withValues(alpha: 0.5),
        elevation: 2,
        title: Image.asset(Images.logoName, width: 120),
        actions: [IconButton(
          icon: GetBuilder<NotificationController>(builder: (notificationController) {

            bool hasNewNotification = false;

            if(notificationController.notificationList != null) {
              hasNewNotification = notificationController.notificationList!.length != notificationController.getSeenNotificationCount();
            }

            return Stack(children: [

              Icon(Icons.notifications, size: 25, color: Theme.of(context).textTheme.bodyLarge!.color),

              hasNewNotification ? Positioned(top: 0, right: 0, child: Container(
                height: 10, width: 10, decoration: BoxDecoration(
                color: Theme.of(context).primaryColor, shape: BoxShape.circle,
                border: Border.all(width: 1, color: Theme.of(context).cardColor),
              ),
              )) : const SizedBox(),

            ]);
          }),
          onPressed: () {
            Get.find<SubscriptionController>().trialEndBottomSheet().then((trialEnd) {
              if(trialEnd) {
                Get.toNamed(RouteHelper.getNotificationRoute());
              }
            });
          },
        )],
      ),

      body: RefreshIndicator(
        onRefresh: () async {
          await _loadData();
        },
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(vertical: Dimensions.paddingSizeDefault, horizontal: Dimensions.paddingSizeSmall),
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(children: [

            GetBuilder<ProfileController>(builder: (profileController) {
              return Column(children: [

                Container(
                  padding: const EdgeInsets.all(Dimensions.paddingSizeSmall),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    color: Theme.of(context).cardColor,
                    boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                  ),
                  child: Row(children: [

                    Expanded(child: Text(
                      'restaurant_temporarily_closed'.tr, style: robotoMedium,
                      maxLines: 1, overflow: TextOverflow.ellipsis,
                    )),

                    profileController.profileModel != null ? Transform.scale(
                      scale: 0.8,
                      child: CupertinoSwitch(
                        value: !profileController.profileModel!.restaurants![0].active!,
                        activeTrackColor: Theme.of(context).primaryColor,
                        inactiveTrackColor: Theme.of(context).primaryColor.withValues(alpha: 0.5),
                        onChanged: (bool isActive) {
                          Get.dialog(ConfirmationDialogWidget(
                            icon: Images.warning,
                            description: isActive ? 'are_you_sure_to_close_restaurant'.tr : 'are_you_sure_to_open_restaurant'.tr,
                            onYesPressed: () {
                              Get.back();
                              Get.find<AuthController>().toggleRestaurantClosedStatus();
                            },
                          ));
                        },
                      ),
                    ) : Shimmer(duration: const Duration(seconds: 2), child: Container(height: 30, width: 50, color: Colors.grey[300])),

                  ]),
                ),
                const SizedBox(height: Dimensions.paddingSizeDefault),

                OrderSummaryCard(profileController: profileController),
                const SizedBox(height: Dimensions.paddingSizeLarge),

                const AdsSectionWidget(),
              ]);
            }),
            const SizedBox(height: Dimensions.paddingSizeLarge),

            GetBuilder<OrderController>(builder: (orderController) {

              List<OrderModel> orderList = [];

              if(orderController.runningOrders != null) {
                orderList = orderController.runningOrders![orderController.orderIndex].orderList;
              }

              return Container(
                constraints: BoxConstraints(minHeight: context.height * 0.4),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  color: Theme.of(context).cardColor,
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                padding: const EdgeInsets.all(Dimensions.paddingSizeSmall),
                child: Column(children: [

                  orderController.runningOrders != null ? SizedBox(
                    height: 40,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      itemCount: orderController.runningOrders!.length,
                      itemBuilder: (context, index) {
                        return OrderButtonWidget(
                          title: orderController.runningOrders![index].status.tr, index: index,
                          orderController: orderController, fromHistory: false,
                        );
                      },
                    ),
                  ) : const SizedBox(),

                  Padding(
                    padding: const EdgeInsets.only(top: Dimensions.paddingSizeDefault),
                    child: Row(mainAxisAlignment: MainAxisAlignment.spaceEvenly, children: [

                      orderController.runningOrders != null ? InkWell(
                        onTap: () => orderController.toggleCampaignOnly(),
                        child: Row(children: [
                          Container(
                            padding: const EdgeInsets.all(2),
                            margin: const EdgeInsets.only(right: Dimensions.paddingSizeExtraSmall),
                            decoration: BoxDecoration(
                              color: orderController.campaignOnly ? Colors.green : Theme.of(context).cardColor,
                              border: Border.all(color: orderController.campaignOnly ? Colors.transparent : Theme.of(context).disabledColor),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.check, size: 14, color: orderController.campaignOnly ? Theme.of(context).cardColor :Theme.of(context).disabledColor,),
                          ),

                          Text(
                            'campaign_order'.tr,
                            style: orderController.campaignOnly ? robotoMedium.copyWith(color: Theme.of(context).textTheme.bodyMedium!.color!)
                                : robotoRegular.copyWith(color: Theme.of(context).disabledColor),
                          ),
                        ]),
                      ) : const SizedBox(),

                      orderController.runningOrders != null ? InkWell(
                        onTap: () => orderController.toggleSubscriptionOnly(),
                        child: Row(children: [
                          Container(
                            padding: const EdgeInsets.all(2),
                            margin: const EdgeInsets.only(right: Dimensions.paddingSizeExtraSmall),
                            decoration: BoxDecoration(
                              color: orderController.subscriptionOnly ? Colors.green : Theme.of(context).cardColor,
                              border: Border.all(color: orderController.subscriptionOnly ? Colors.transparent : Theme.of(context).disabledColor),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.check, size: 14, color: orderController.subscriptionOnly ? Theme.of(context).cardColor :Theme.of(context).disabledColor,),
                          ),

                          Text(
                            'subscription_order'.tr,
                            style: orderController.subscriptionOnly ? robotoMedium.copyWith(color: Theme.of(context).textTheme.bodyMedium!.color!)
                                : robotoRegular.copyWith(color: Theme.of(context).disabledColor),
                          ),
                        ]),
                      ) : const SizedBox(),

                    ]),
                  ),

                  const Divider(height: Dimensions.paddingSizeOverLarge),

                  orderController.runningOrders != null ? orderList.isNotEmpty ? ListView.builder(
                    physics: const NeverScrollableScrollPhysics(),
                    shrinkWrap: true,
                    itemCount: orderList.length,
                    itemBuilder: (context, index) {
                      return OrderWidget(orderModel: orderList[index], hasDivider: index != orderList.length-1, isRunning: true);
                    },
                  ) : Padding(
                    padding: const EdgeInsets.only(top: 50),
                    child: Center(child: Text('no_order_found'.tr)),
                  ) : ListView.builder(
                    physics: const NeverScrollableScrollPhysics(),
                    shrinkWrap: true,
                    itemCount: 10,
                    itemBuilder: (context, index) {
                      return OrderShimmerWidget(isEnabled: orderController.runningOrders == null);
                    },
                  ),

                ]),
              );
            }),

          ]),
        ),
      ),
    );
  }
}