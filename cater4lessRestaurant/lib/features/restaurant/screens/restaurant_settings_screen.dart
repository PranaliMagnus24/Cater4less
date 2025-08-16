import 'dart:developer';
import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:stackfood_multivendor_restaurant/common/controllers/theme_controller.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_app_bar_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_button_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_dropdown_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_image_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_ink_well_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_snackbar_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_text_field_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_text_form_field_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/switch_button_widget.dart';
import 'package:stackfood_multivendor_restaurant/features/auth/controllers/auth_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/language/controllers/localization_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/profile/controllers/profile_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/restaurant/controllers/restaurant_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/splash/controllers/splash_controller.dart';
import 'package:stackfood_multivendor_restaurant/common/models/config_model.dart';
import 'package:stackfood_multivendor_restaurant/features/restaurant/domain/models/product_model.dart';
import 'package:stackfood_multivendor_restaurant/features/profile/domain/models/profile_model.dart';
import 'package:stackfood_multivendor_restaurant/features/restaurant/widgets/daily_time_widget.dart';
import 'package:stackfood_multivendor_restaurant/helper/type_converter.dart';
import 'package:stackfood_multivendor_restaurant/util/dimensions.dart';
import 'package:stackfood_multivendor_restaurant/util/styles.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class RestaurantSettingsScreen extends StatefulWidget {
  final Restaurant restaurant;
  const RestaurantSettingsScreen({super.key, required this.restaurant});

  @override
  State<RestaurantSettingsScreen> createState() => _RestaurantSettingsScreenState();
}

class _RestaurantSettingsScreenState extends State<RestaurantSettingsScreen> {

  final List<TextEditingController> _nameController = [];
  final TextEditingController _contactController = TextEditingController();
  final List<TextEditingController> _addressController = [];
  final TextEditingController _orderAmountController = TextEditingController();
  final TextEditingController _minimumChargeController = TextEditingController();
  final TextEditingController _maximumChargeController = TextEditingController();
  final TextEditingController _perKmChargeController = TextEditingController();
  final TextEditingController _gstController = TextEditingController();
  final TextEditingController _extraPackagingController = TextEditingController();
  TextEditingController _characteristicSuggestionController = TextEditingController();
  TextEditingController _c = TextEditingController();
  final List<TextEditingController> _metaTitleController = [];
  final List<TextEditingController> _metaDescriptionController = [];
  final TextEditingController _dineInAdvanceTimeController = TextEditingController();
  final TextEditingController _customerOrderDaysController = TextEditingController();
  final TextEditingController _freeDeliveryDistanceController = TextEditingController();
  final TextEditingController _tagController = TextEditingController();

  final List<FocusNode> _nameNode = [];
  final FocusNode _contactNode = FocusNode();
  final List<FocusNode> _addressNode = [];
  final FocusNode _orderAmountNode = FocusNode();
  final FocusNode _minimumChargeNode = FocusNode();
  final FocusNode _maximumChargeNode = FocusNode();
  final FocusNode _perKmChargeNode = FocusNode();
  final List<FocusNode> _metaTitleNode = [];
  final List<FocusNode> _metaDescriptionNode = [];
  final FocusNode _metaKeyWordNode = FocusNode();
  final FocusNode _customerOrderDaysNode = FocusNode();
  final FocusNode _freeDeliveryDistanceNode = FocusNode();
  late Restaurant _restaurant;
  final List<Language>? _languageList = Get.find<SplashController>().configModel!.language;
  final List<Translation>? translation = Get.find<ProfileController>().profileModel!.translations!;
  List<DropdownItem<int>> timeList = [];

  @override
  void initState() {
    super.initState();

    _getTimeList();
    Get.find<RestaurantController>().initRestaurantData(widget.restaurant);

    for(int index=0; index<_languageList!.length; index++) {

      _nameController.add(TextEditingController());
      _addressController.add(TextEditingController());
      _metaTitleController.add(TextEditingController());
      _metaDescriptionController.add(TextEditingController());
      _nameNode.add(FocusNode());
      _addressNode.add(FocusNode());
      _metaTitleNode.add(FocusNode());
      _metaDescriptionNode.add(FocusNode());

      for (var trans in translation!) {
        if(_languageList[index].key == trans.locale && trans.key == 'name') {
          _nameController[index] = TextEditingController(text: trans.value);
        }else if(_languageList[index].key == trans.locale && trans.key == 'address') {
          _addressController[index] = TextEditingController(text: trans.value);
        }else if (_languageList[index].key == trans.locale && trans.key == 'meta_title') {
          _metaTitleController[index] = TextEditingController(text: trans.value);
        }  else if (_languageList[index].key == trans.locale && trans.key == 'meta_description') {
          _metaDescriptionController[index] = TextEditingController(text: trans.value);
        }
      }
    }

    _contactController.text = widget.restaurant.phone!;
    _orderAmountController.text = widget.restaurant.minimumOrder.toString();
    _minimumChargeController.text = widget.restaurant.minimumShippingCharge != null ? widget.restaurant.minimumShippingCharge.toString() : '';
    _maximumChargeController.text = widget.restaurant.maximumShippingCharge != null ? widget.restaurant.maximumShippingCharge.toString() : '';
    _perKmChargeController.text = widget.restaurant.perKmShippingCharge != null ? widget.restaurant.perKmShippingCharge.toString() : '';
    _gstController.text = widget.restaurant.gstCode!;
    _extraPackagingController.text = widget.restaurant.extraPackagingAmount != null ? widget.restaurant.extraPackagingAmount.toString() : '';
    _restaurant = widget.restaurant;
    _dineInAdvanceTimeController.text = widget.restaurant.scheduleAdvanceDineInBookingDuration != null ? widget.restaurant.scheduleAdvanceDineInBookingDuration.toString() : '';
    _customerOrderDaysController.text = widget.restaurant.customOrderDate != null ? widget.restaurant.customOrderDate.toString() : '';
    _freeDeliveryDistanceController.text = widget.restaurant.freeDeliveryDistance != null ? widget.restaurant.freeDeliveryDistance.toString() : '';
  }

  void _getTimeList() {
    for(int i=0; i<Get.find<RestaurantController>().timeTypes.length; i++) {
      timeList.add(DropdownItem<int>(value: i, child: SizedBox(
        child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Text(Get.find<RestaurantController>().timeTypes[i].tr),
        ),
      )));
    }
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

      appBar: CustomAppBarWidget(title: 'restaurant_settings'.tr),

      body: GetBuilder<RestaurantController>(builder: (restController) {

        List<int> cuisines0 = [];
        if(restController.cuisineModel != null) {
          for(int index=0; index<restController.cuisineModel!.cuisines!.length; index++) {
            if(restController.cuisineModel!.cuisines![index].status == 1 && !restController.selectedCuisines!.contains(index)) {
              cuisines0.add(index);
            }
          }
        }

        List<int> characteristicSuggestion = [];
        if(restController.characteristicSuggestionList != null) {
          for(int index = 0; index<restController.characteristicSuggestionList!.length; index++) {
            characteristicSuggestion.add(index);
          }
        }

        return Column(children: [

          Expanded(child: SingleChildScrollView(
            padding: const EdgeInsets.all(Dimensions.paddingSizeSmall),
            physics: const BouncingScrollPhysics(),
            child: Column(children: [

              Align(
                alignment: Alignment.topLeft,
                child: Text(
                  'logo'.tr,
                  style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeExtraSmall),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeExtraLarge),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Align(alignment: Alignment.center, child: Stack(children: [

                  ClipRRect(
                    borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                    child: restController.pickedLogo != null ? GetPlatform.isWeb ? Image.network(
                      restController.pickedLogo!.path, width: 150, height: 120, fit: BoxFit.cover) : Image.file(
                      File(restController.pickedLogo!.path), width: 150, height: 120, fit: BoxFit.cover) : CustomImageWidget(
                      image: '${widget.restaurant.logoFullUrl}',
                      height: 120, width: 150, fit: BoxFit.cover,
                    ),
                  ),

                  Positioned(
                    bottom: 0, right: 0, top: 0, left: 0,
                    child: InkWell(
                      onTap: () => restController.pickImage(true, false),
                      child: Container(
                        decoration: BoxDecoration(
                          color: Colors.black.withValues(alpha: 0.3), borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                          border: Border.all(width: 1, color: Theme.of(context).primaryColor),
                        ),
                        child: Container(
                          margin: const EdgeInsets.all(25),
                          decoration: BoxDecoration(
                            border: Border.all(width: 2, color: Colors.white),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.camera_alt, color: Colors.white),
                        ),
                      ),
                    ),
                  ),

                ])),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: ListView.builder(
                  itemCount: _languageList!.length,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemBuilder: (context, index) {
                    return Padding(
                      padding: EdgeInsets.only(bottom: index == _languageList.length-1 ? 0 : Dimensions.paddingSizeLarge),
                      child: CustomTextFormFieldWidget(
                        hintText: '${'restaurant_name'.tr} (${_languageList[index].value!})',
                        controller: _nameController[index],
                        focusNode: _nameNode[index],
                        nextFocus: index != _languageList.length-1 ? _nameNode[index+1] : _contactNode,
                        capitalization: TextCapitalization.words,
                        inputType: TextInputType.name,
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: CustomTextFormFieldWidget(
                  hintText: 'contact_number'.tr,
                  controller: _contactController,
                  focusNode: _contactNode,
                  nextFocus: _addressNode[0],
                  inputType: TextInputType.phone,
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: ListView.builder(
                  itemCount: _languageList.length,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemBuilder: (context, index) {
                    return Padding(
                      padding: EdgeInsets.only(bottom: index == _languageList.length-1 ? 0 : Dimensions.paddingSizeLarge),
                      child: CustomTextFormFieldWidget(
                        hintText: '${'address'.tr} (${_languageList[index].value!})',
                        controller: _addressController[index],
                        focusNode: _addressNode[index],
                        nextFocus: index != _languageList.length-1 ? _addressNode[index+1] : _orderAmountNode,
                        inputType: TextInputType.streetAddress,
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              _restaurant.selfDeliverySystem == 1 ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(children: [

                  Row(children: [

                    Expanded(child: Text(
                      'custom_date_order_status'.tr,
                      style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                    )),

                    Switch(
                      value: restController.customDateOrderEnabled!,
                      activeColor: Theme.of(context).primaryColor,
                      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      onChanged: (bool isActive) => restController.toggleCustomDateOrder(),
                    ),

                  ]),

                ]),
              ) : const SizedBox(),
              SizedBox(height: _restaurant.selfDeliverySystem == 1 ? Dimensions.paddingSizeLarge : 0),

              _restaurant.selfDeliverySystem == 1 ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(children: [

                  CustomTextFormFieldWidget(
                    hintText: 'customer_can_order_within_days'.tr,
                    controller: _customerOrderDaysController,
                    focusNode: _customerOrderDaysNode,
                    inputAction: TextInputAction.done,
                    inputType: TextInputType.phone,
                    isEnabled: restController.customDateOrderEnabled!,
                  ),

                ]),
              ) : const SizedBox(),
              SizedBox(height: _restaurant.selfDeliverySystem == 1 ? Dimensions.paddingSizeLarge : 0),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: CustomTextFormFieldWidget(
                  hintText: 'minimum_order_amount'.tr,
                  controller: _orderAmountController,
                  focusNode: _orderAmountNode,
                  nextFocus: _restaurant.selfDeliverySystem == 1 ? _perKmChargeNode : null,
                  inputAction: _restaurant.selfDeliverySystem == 0 ? null : TextInputAction.done,
                  inputType: TextInputType.number,
                  isAmount: true,
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              _restaurant.selfDeliverySystem == 1 ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: CustomTextFormFieldWidget(
                  hintText: 'per_km_delivery_charge'.tr,
                  controller: _perKmChargeController,
                  focusNode: _restaurant.selfDeliverySystem == 1 ? _perKmChargeNode : null,
                  nextFocus: _restaurant.selfDeliverySystem == 1 ? _minimumChargeNode : null,
                  inputType: TextInputType.number,
                  isAmount: true,
                ),
              ) : const SizedBox(),
              SizedBox(height: _restaurant.selfDeliverySystem == 1 ? Dimensions.paddingSizeLarge : 0),

              _restaurant.selfDeliverySystem == 1 ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(children: [
                  CustomTextFormFieldWidget(
                    hintText: 'minimum_delivery_charge'.tr,
                    controller: _minimumChargeController,
                    focusNode: _minimumChargeNode,
                    nextFocus: _maximumChargeNode,
                    inputType: TextInputType.number,
                    isAmount: true,
                  ),
                  const SizedBox(height: Dimensions.paddingSizeLarge),

                  CustomTextFormFieldWidget(
                    hintText: 'maximum_delivery_charge'.tr,
                    controller: _maximumChargeController,
                    focusNode: _maximumChargeNode,
                    inputAction: TextInputAction.done,
                    inputType: TextInputType.number,
                    isAmount: true,
                  ),
                ]),
              ) : const SizedBox(),
              SizedBox(height: _restaurant.selfDeliverySystem == 1 ? Dimensions.paddingSizeLarge : 0),

              _restaurant.selfDeliverySystem == 1 ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(children: [

                  Row(children: [

                    Expanded(child: Text(
                      'free_delivery_distance_km'.tr,
                      style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                    )),

                    Switch(
                      value: restController.freeDeliveryDistanceEnabled!,
                      activeColor: Theme.of(context).primaryColor,
                      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      onChanged: (bool isActive) => restController.toggleFreeDeliveryDistance(),
                    ),

                  ]),
                  const SizedBox(height: Dimensions.paddingSizeSmall),

                  CustomTextFormFieldWidget(
                    hintText: 'free_delivery_distance_km'.tr,
                    controller: _freeDeliveryDistanceController,
                    focusNode: _freeDeliveryDistanceNode,
                    inputAction: TextInputAction.done,
                    showTitle: false,
                    isEnabled: restController.freeDeliveryDistanceEnabled!,
                  ),

                ]),
              ) : const SizedBox(),
              SizedBox(height: _restaurant.selfDeliverySystem == 1 ? Dimensions.paddingSizeLarge : 0),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeLarge),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

                  Text('tag'.tr, style: robotoMedium),
                  const SizedBox(height: Dimensions.paddingSizeSmall),

                  Row(children: [

                    Expanded(
                      flex: 8,
                      child: CustomTextFieldWidget(
                        hintText: 'tag'.tr,
                        labelText: 'tag'.tr,
                        showTitle: false,
                        controller: _tagController,
                        inputAction: TextInputAction.done,
                        onSubmit: (name){
                          if(name.isNotEmpty) {
                            restController.setRestaurantTag(name);
                            _tagController.text = '';
                          }
                        },
                      ),
                    ),
                    const SizedBox(width: Dimensions.paddingSizeDefault),

                    Expanded(
                      flex: 2,
                      child: CustomButtonWidget(buttonText: 'add'.tr, onPressed: (){
                        if(_tagController.text.isNotEmpty) {
                          restController.setRestaurantTag(_tagController.text.trim());
                          _tagController.text = '';
                        }
                      }),
                    ),

                  ]),
                  SizedBox(height: restController.restaurantTagList.isNotEmpty ? Dimensions.paddingSizeSmall : 0),

                  restController.restaurantTagList.isNotEmpty ? SizedBox(
                    height: 40,
                    child: ListView.builder(
                      shrinkWrap: true, scrollDirection: Axis.horizontal,
                      itemCount: restController.restaurantTagList.length,
                      itemBuilder: (context, index){
                        return Container(
                          margin: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeExtraSmall),
                          padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall),
                          decoration: BoxDecoration(color: Theme.of(context).disabledColor.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(Dimensions.radiusSmall)),
                          child: Center(child: Row(children: [

                            Text(restController.restaurantTagList[index]!, style: robotoMedium.copyWith(color: Theme.of(context).disabledColor)),
                            const SizedBox(width: Dimensions.paddingSizeExtraSmall),

                            InkWell(onTap: () => restController.removeRestaurantTag(index), child: Icon(Icons.clear, size: 18, color: Theme.of(context).disabledColor)),

                          ])),
                        );
                      },
                    ),
                  ) : const SizedBox(),

                ]),
              ),
              const SizedBox(height: Dimensions.paddingSizeDefault),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: ListView.builder(
                  itemCount: _languageList.length,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemBuilder: (context, index) {
                    return Padding(
                      padding: EdgeInsets.only(bottom: index == _languageList.length-1 ? 0 : Dimensions.paddingSizeLarge),
                      child: CustomTextFormFieldWidget(
                        hintText:  '${'meta_title'.tr}  (${_languageList[index].value!})',
                        controller: _metaTitleController[index],
                        focusNode: _metaTitleNode[index],
                        nextFocus: index != _languageList.length-1 ? _metaTitleNode[index+1] : _metaDescriptionNode[0],
                        capitalization: TextCapitalization.words,
                        inputType: TextInputType.name,
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: ListView.builder(
                  itemCount: _languageList.length,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemBuilder: (context, index) {
                    return Padding(
                      padding: EdgeInsets.only(bottom: index == _languageList.length-1 ? 0 : Dimensions.paddingSizeLarge),
                      child: CustomTextFormFieldWidget(
                        hintText:  '${'meta_description'.tr}  (${_languageList[index].value!})',
                        controller: _metaDescriptionController[index],
                        focusNode: _metaDescriptionNode[index],
                        nextFocus: index != _languageList.length-1 ? _metaDescriptionNode[index+1] : _metaKeyWordNode,
                        capitalization: TextCapitalization.words,
                        inputType: TextInputType.name,
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Column(children: [

                Align(
                  alignment: Alignment.topLeft,
                  child: Text(
                    'set_restaurant_characteristics'.tr,
                    style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                  ),
                ),
                const SizedBox(height: Dimensions.paddingSizeExtraSmall),

                Align(
                  alignment: Alignment.topLeft,
                  child: Text(
                    'select_the_restaurant_type_that_best_represents_your_establishment'.tr,
                    style: robotoRegular.copyWith(color: Theme.of(context).disabledColor),
                  ),
                ),
                const SizedBox(height: Dimensions.paddingSizeSmall),

                Container(
                  padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeDefault),
                  decoration: BoxDecoration(
                    color: Theme.of(context).cardColor,
                    borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                  ),
                  child: Column(children: [

                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeExtraSmall, vertical: Dimensions.paddingSizeSmall),
                      decoration: BoxDecoration(
                        color: Theme.of(context).disabledColor.withValues(alpha: 0.07),
                        borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                      ),
                      child: Row(children: [

                        Expanded(
                          flex: 8,
                          child: Autocomplete<int>(
                            optionsBuilder: (TextEditingValue value) {
                              if(value.text.isEmpty) {
                                return const Iterable<int>.empty();
                              }else {
                                return characteristicSuggestion.where((characteristic) => restController.characteristicSuggestionList![characteristic]!.toLowerCase().contains(value.text.toLowerCase()));
                              }
                            },
                            optionsViewBuilder: (context, onAutoCompleteSelect, options) {
                              List<int> result = TypeConverter.convertIntoListOfInteger(options.toString());

                              return Align(
                                  alignment: Alignment.topLeft,
                                  child: Material(
                                    color: Theme.of(context).primaryColorLight,
                                    elevation: 4.0,
                                    child: Container(
                                      color: Theme.of(context).cardColor,
                                        width: MediaQuery.of(context).size.width - 110,
                                        child: ListView.separated(
                                          shrinkWrap: true,
                                          padding: const EdgeInsets.all(8.0),
                                          itemCount: result.length,
                                          separatorBuilder: (context, i) {
                                            return const Divider(height: 0,);
                                          },
                                          itemBuilder: (BuildContext context, int index) {
                                            return CustomInkWellWidget(
                                              onTap: () {
                                                if(restController.selectedCharacteristicsList!.length >= 5) {
                                                  showCustomSnackBar('you_can_select_or_add_maximum_5_characteristics'.tr, isError: true);
                                                }else {
                                                  _characteristicSuggestionController.text = '';
                                                  restController.setSelectedCharacteristicsIndex(result[index], true);
                                                }
                                              },
                                              child: Padding(
                                                padding: const EdgeInsets.symmetric(vertical: Dimensions.paddingSizeSmall),
                                                child: Text(restController.characteristicSuggestionList![result[index]]!),
                                              ),
                                            );
                                          },
                                        )
                                    ),
                                  )
                              );
                            },
                            fieldViewBuilder: (context, controller, node, onComplete) {
                              _characteristicSuggestionController = controller;
                              return Container(
                                height: 50,
                                decoration: BoxDecoration(borderRadius: BorderRadius.circular(Dimensions.radiusSmall)),
                                child: TextField(
                                  // textDirection: TextDirection.rtl,
                                  controller: controller,
                                  focusNode: node,
                                  onEditingComplete: () {
                                    onComplete();
                                    controller.text = '';
                                  },
                                  decoration: InputDecoration(
                                    hintText: 'enter_restaurant_type'.tr,
                                    hintStyle: robotoRegular.copyWith(color: Theme.of(context).disabledColor.withValues(alpha: 0.8)),
                                    enabledBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                                      borderSide: BorderSide(color: Theme.of(context).disabledColor.withValues(alpha: 0.5)),
                                    ),
                                    focusedBorder: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                                      borderSide: BorderSide(color: Theme.of(context).disabledColor.withValues(alpha: 0.5)),
                                    ),
                                  ),
                                ),
                              );
                            },
                            displayStringForOption: (value) => restController.characteristicSuggestionList![value]!,
                            onSelected: (int value) {

                              if(restController.selectedCharacteristicsList!.length >= 5) {
                                showCustomSnackBar('you_can_select_or_add_maximum_5_characteristics'.tr, isError: true);
                              }else {
                                _characteristicSuggestionController.text = '';
                                restController.setSelectedCharacteristicsIndex(value, true);
                              }

                            },
                          ),
                        ),

                        const SizedBox(width: Dimensions.paddingSizeDefault),

                        Expanded(
                          flex: 2,
                          child: CustomButtonWidget(buttonText: 'add'.tr, onPressed: (){

                            if(restController.selectedCharacteristicsList!.length >= 5) {
                              showCustomSnackBar('you_can_select_or_add_maximum_5_characteristics'.tr, isError: true);
                            }else{
                              if(_characteristicSuggestionController.text.isNotEmpty) {
                                restController.setCharacteristics(_characteristicSuggestionController.text.trim());
                                _characteristicSuggestionController.text = '';
                              }
                            }

                          }),
                        ),

                      ]),
                    ),
                    SizedBox(height: restController.selectedCharacteristicsList!.isNotEmpty ? Dimensions.paddingSizeSmall : 0),

                    restController.selectedCharacteristicsList != null ? SizedBox(
                      height: restController.selectedCharacteristicsList!.isNotEmpty ? 40 : 0,
                      child: ListView.builder(
                        itemCount: restController.selectedCharacteristicsList!.length,
                        scrollDirection: Axis.horizontal,
                        itemBuilder: (context, index) {
                          return Container(
                            padding: EdgeInsets.only(left: Dimensions.paddingSizeExtraSmall, right: Get.find<LocalizationController>().isLtr ? 0 : Dimensions.paddingSizeSmall),
                            margin: const EdgeInsets.only(right: Dimensions.paddingSizeSmall),
                            decoration: BoxDecoration(
                              color: Theme.of(context).disabledColor.withValues(alpha: 0.2),
                              borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                            ),
                            child: Row(children: [

                              Text(
                                restController.selectedCharacteristicsList![index]!,
                                style: robotoRegular.copyWith(color: Theme.of(context).disabledColor.withValues(alpha: 0.7)),
                              ),

                              InkWell(
                                onTap: () => restController.removeCharacteristic(index),
                                child: Padding(
                                  padding: const EdgeInsets.all(Dimensions.paddingSizeExtraSmall),
                                  child: Icon(Icons.close, size: 15, color: Theme.of(context).disabledColor.withValues(alpha: 0.7)),
                                ),
                              ),

                            ]),
                          );
                        },
                      ),
                    ) : const SizedBox(),

                  ]),
                ),
              ]),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Column(children: [

                Align(
                  alignment: Alignment.topLeft,
                  child: Text(
                    'cuisines'.tr,
                    style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                  ),
                ),
                const SizedBox(height: Dimensions.paddingSizeExtraSmall),

                Container(
                  padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeDefault),
                  decoration: BoxDecoration(
                    color: Theme.of(context).cardColor,
                    borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                  ),
                  child: Column(children: [

                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeExtraSmall, vertical: Dimensions.paddingSizeSmall),
                      decoration: BoxDecoration(
                        color: Theme.of(context).disabledColor.withValues(alpha: 0.07),
                        borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                      ),
                      child: Autocomplete<int>(
                        optionsBuilder: (TextEditingValue value) {
                          if(value.text.isEmpty) {
                            return const Iterable<int>.empty();
                          }else {
                            return cuisines0.where((cuisine) => restController.cuisineModel!.cuisines![cuisine].name!.toLowerCase().contains(value.text.toLowerCase()));
                          }
                        },
                        optionsViewBuilder: (context, onAutoCompleteSelect, options) {
                          List<int> result = TypeConverter.convertIntoListOfInteger(options.toString());

                          return Align(
                              alignment: Alignment.topLeft,
                              child: Material(
                                color: Theme.of(context).primaryColorLight,
                                elevation: 4.0,
                                child: Container(
                                    color: Theme.of(context).cardColor,
                                    width: MediaQuery.of(context).size.width - 50,
                                    child: ListView.separated(
                                      shrinkWrap: true,
                                      padding: const EdgeInsets.all(8.0),
                                      itemCount: result.length,
                                      separatorBuilder: (context, i) {
                                        return const Divider(height: 0,);
                                      },
                                      itemBuilder: (BuildContext context, int index) {
                                        return CustomInkWellWidget(
                                          onTap: () {
                                            _c.text = '';
                                            restController.setSelectedCuisineIndex(result[index], true);
                                          },
                                          child: Padding(
                                            padding: const EdgeInsets.symmetric(vertical: Dimensions.paddingSizeSmall),
                                            child: Text(restController.cuisineModel!.cuisines![result[index]].name!),
                                          ),
                                        );
                                      },
                                    )
                                ),
                              )
                          );
                        },
                        fieldViewBuilder: (context, controller, node, onComplete) {
                          _c = controller;
                          return Container(
                            height: 50,
                            decoration: BoxDecoration(
                              //color: Theme.of(context).cardColor,
                              borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                            ),
                            child: TextField(
                              controller: controller,
                              focusNode: node,
                              onEditingComplete: () {
                                onComplete();
                                controller.text = '';
                              },
                              decoration: InputDecoration(
                                hintText: 'cuisines'.tr,
                                hintStyle: robotoRegular.copyWith(color: Theme.of(context).disabledColor.withValues(alpha: 0.8)),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                                  borderSide: BorderSide(color: Theme.of(context).disabledColor.withValues(alpha: 0.5)),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                                  borderSide: BorderSide(color: Theme.of(context).disabledColor.withValues(alpha: 0.5)),
                                ),
                              ),
                            ),
                          );
                        },
                        displayStringForOption: (value) => restController.cuisineModel!.cuisines![value].name!,
                        onSelected: (int value) {
                          _c.text = '';
                          restController.setSelectedCuisineIndex(value, true);
                        },
                      ),
                    ),
                    SizedBox(height: restController.selectedCuisines!.isNotEmpty ? Dimensions.paddingSizeSmall : 0),

                    SizedBox(
                      height: restController.selectedCuisines!.isNotEmpty ? 40 : 0,
                      child: ListView.builder(
                        itemCount: restController.selectedCuisines!.length,
                        scrollDirection: Axis.horizontal,
                        itemBuilder: (context, index) {
                          return Container(
                            padding: EdgeInsets.only(left: Dimensions.paddingSizeExtraSmall, right: Get.find<LocalizationController>().isLtr ? 0 : Dimensions.paddingSizeSmall),
                            margin: const EdgeInsets.only(right: Dimensions.paddingSizeSmall),
                            decoration: BoxDecoration(
                              color: Theme.of(context).disabledColor.withValues(alpha: 0.2),
                              borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                            ),
                            child: Row(children: [

                              Text(
                                restController.cuisineModel!.cuisines![restController.selectedCuisines![index]].name!,
                                style: robotoRegular.copyWith(color: Theme.of(context).disabledColor.withValues(alpha: 0.7)),
                              ),

                              InkWell(
                                onTap: () => restController.removeCuisine(index),
                                child: Padding(
                                  padding: const EdgeInsets.all(Dimensions.paddingSizeExtraSmall),
                                  child: Icon(Icons.close, size: 15, color: Theme.of(context).disabledColor.withValues(alpha: 0.7)),
                                ),
                              ),

                            ]),
                          );
                        },
                      ),
                    ),

                  ]),
                ),
              ]),
              const SizedBox(height: Dimensions.paddingSizeLarge),


              Get.find<SplashController>().configModel!.toggleVegNonVeg! ? Column(children: [

                Align(alignment: Alignment.centerLeft, child: Text('food_type'.tr, style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge))),
                const SizedBox(height: Dimensions.paddingSizeExtraSmall),

                Container(
                  padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeExtraSmall),
                  decoration: BoxDecoration(
                    color: Theme.of(context).cardColor,
                    borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                  ),
                  child: Row(children: [

                    Expanded(child: InkWell(
                      onTap: () => restController.setRestVeg(!restController.isRestVeg!, true),
                      child: Row(children: [

                        Checkbox(
                          value: restController.isRestVeg,
                          onChanged: (bool? isActive) => restController.setRestVeg(isActive, true),
                          activeColor: Theme.of(context).primaryColor,
                        ),

                        Text('veg'.tr),

                      ]),
                    )),
                    const SizedBox(width: Dimensions.paddingSizeSmall),

                    Expanded(child: InkWell(
                      onTap: () => restController.setRestNonVeg(!restController.isRestNonVeg!, true),
                      child: Row(children: [

                        Checkbox(
                          value: restController.isRestNonVeg,
                          onChanged: (bool? isActive) => restController.setRestNonVeg(isActive, true),
                          activeColor: Theme.of(context).primaryColor,
                        ),

                        Text('non_veg'.tr),

                      ]),
                    )),
                  ]),
                ),
                const SizedBox(height: Dimensions.paddingSizeLarge),

              ]) : const SizedBox(),

              Get.find<SplashController>().configModel!.extraPackagingChargeStatus! ? Column(children: [

                Row(children: [

                  Expanded(
                    child: Text('extra_package_charge'.tr, style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),),
                  ),

                  Transform.scale(
                    scale: 0.7,
                    child: CupertinoSwitch(
                      value: restController.isExtraPackagingEnabled!,
                      activeTrackColor: Theme.of(context).primaryColor,
                      inactiveTrackColor: Theme.of(context).primaryColor.withValues(alpha: 0.5),
                      onChanged: (bool isActive) => restController.toggleExtraPackaging(),
                    ),
                  ),

                ]),
                const SizedBox(height: Dimensions.paddingSizeExtraSmall),

                Align(
                  alignment: Alignment.centerLeft,
                  child: Text('leave_the_input_box_empty_to_not_offering_extra_packaging_charge'.tr, style: robotoRegular.copyWith(color: Theme.of(context).disabledColor)),
                ),
                const SizedBox(height: Dimensions.paddingSizeSmall),

                Container(
                  padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                  decoration: BoxDecoration(
                    color: Theme.of(context).cardColor,
                    borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                  ),
                  child: Container(
                    padding: const EdgeInsets.only(top: Dimensions.paddingSizeExtraSmall, bottom: Dimensions.paddingSizeExtraLarge),
                    decoration: BoxDecoration(
                      color: Theme.of(context).disabledColor.withValues(alpha: 0.07),
                      borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    ),
                    child: Column(children: [

                      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [

                        InkWell(
                          onTap: () {
                            restController.setExtraPackagingSelectedValue(0);
                          },
                          child: Row(children: [

                            Radio(
                              value: 0,
                              groupValue: restController.extraPackagingSelectedValue,
                              onChanged: (value) {
                                restController.setExtraPackagingSelectedValue(value!);
                              },
                            ),

                            Text('optional'.tr),

                          ]),
                        ),
                        const SizedBox(width: Dimensions.paddingSizeSmall),

                        Padding(
                          padding: const EdgeInsets.only(right: Dimensions.paddingSizeSmall),
                          child: InkWell(
                            onTap: () {
                              restController.setExtraPackagingSelectedValue(1);
                            },
                            child: Row(children: [

                              Radio(
                                value: 1,
                                groupValue: restController.extraPackagingSelectedValue,
                                onChanged: (value) {
                                  restController.setExtraPackagingSelectedValue(value!);
                                },
                              ),

                              Text('mandatory'.tr),

                            ]),
                          ),
                        ),

                      ]),
                      const SizedBox(height: Dimensions.paddingSizeSmall),

                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeDefault),
                        child: CustomTextFormFieldWidget(
                          hintText: 'extra_packaging'.tr,
                          controller: _extraPackagingController,
                          inputAction: TextInputAction.done,
                          showTitle: false,
                          isAmount: true,
                          isEnabled: restController.isExtraPackagingEnabled,
                        ),
                      ),

                    ]),
                  ),
                ),
                const SizedBox(height: Dimensions.paddingSizeLarge),

              ]) : const SizedBox(),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(children: [

                  Row(children: [

                    Expanded(child: Text(
                      'gst'.tr,
                      style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                    )),

                    Switch(
                      value: restController.isGstEnabled!,
                      activeColor: Theme.of(context).primaryColor,
                      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      onChanged: (bool isActive) => restController.toggleGst(),
                    ),

                  ]),
                  const SizedBox(height: Dimensions.paddingSizeSmall),

                  CustomTextFormFieldWidget(
                    hintText: 'gst'.tr,
                    controller: _gstController,
                    inputAction: TextInputAction.done,
                    showTitle: false,
                    isEnabled: restController.isGstEnabled,
                  ),

                ]),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Column(children: [

                  Row(children: [

                    Expanded(child: Text(
                      'dine_in'.tr,
                      style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                    )),

                    Switch(
                      value: restController.isDineInEnabled!,
                      activeColor: Theme.of(context).primaryColor,
                      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      onChanged: (bool isActive) => restController.toggleDineIn(),
                    ),

                  ]),
                  const SizedBox(height: Dimensions.paddingSizeSmall),

                  Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

                    Text(
                      'minimum_time_for_dine_in_order'.tr,
                      style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                      maxLines: 1, overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: Dimensions.paddingSizeExtraSmall),

                    Container(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                        border: Border.all(color: Theme.of(context).disabledColor.withValues(alpha: 0.5)),
                      ),
                      child: Row(children: [
                          Expanded(
                            flex: 3,
                            child: CustomTextFormFieldWidget(
                              hintText: 'write_time'.tr,
                              controller: _dineInAdvanceTimeController,
                              inputAction: TextInputAction.done,
                              showTitle: false,
                              isEnabled: restController.isDineInEnabled,
                              isBorderEnabled: false,
                              inputType: TextInputType.number,
                            ),
                          ),

                          Expanded(
                            flex: 1,
                            child: Container(
                              decoration: BoxDecoration(
                                color: Theme.of(context).disabledColor.withValues(alpha: 0.2),
                                borderRadius: const BorderRadius.only(
                                  topRight: Radius.circular(Dimensions.radiusDefault),
                                  bottomRight: Radius.circular(Dimensions.radiusDefault),
                                ),
                              ),
                              child: CustomDropdownWidget<int>(
                                onChange: (int? value, int index) {
                                  restController.setTimeType(type: restController.timeTypes[index]);
                                },
                                dropdownButtonStyle: DropdownButtonStyle(
                                  height: 45,
                                  padding: const EdgeInsets.symmetric(
                                    vertical: Dimensions.paddingSizeExtraSmall,
                                    horizontal: Dimensions.paddingSizeExtraSmall,
                                  ),
                                  primaryColor: Theme.of(context).textTheme.bodyLarge!.color,
                                ),
                                dropdownStyle: DropdownStyle(
                                  elevation: 10,
                                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                                  padding: const EdgeInsets.all(Dimensions.paddingSizeExtraSmall),
                                ),
                                items: timeList,
                                child: Padding(
                                  padding: const EdgeInsets.only(left: 8),
                                  child: Text(restController.selectedTimeType.tr),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ]),

                ]),
              ),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Column(children: [

                Align(alignment: Alignment.centerLeft, child: Text(
                  'daily_schedule_time'.tr,
                  style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge),
                )),
                const SizedBox(height: Dimensions.paddingSizeExtraSmall),

                Container(
                  padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
                  decoration: BoxDecoration(
                    color: Theme.of(context).cardColor,
                    borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                    boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                  ),
                  child: ListView.builder(
                    physics: const NeverScrollableScrollPhysics(),
                    shrinkWrap: true,
                    itemCount: 7,
                    itemBuilder: (context, index) {
                      return DailyTimeWidget(weekDay: index);
                    },
                  ),
                ),

              ]),
              const SizedBox(height: Dimensions.paddingSizeLarge),

              Get.find<SplashController>().configModel!.scheduleOrder! ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeExtraSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Row(children: [

                  const Icon(Icons.alarm_add, size: 25),
                  const SizedBox(width: Dimensions.paddingSizeSmall),

                  Expanded(child: Text('schedule_order'.tr, style: robotoRegular)),

                  Switch(
                    thumbIcon: thumbIcon,
                    value: restController.scheduleOrder,
                    onChanged: (onChanged){
                      restController.setScheduleOrder(onChanged);
                    },
                  ),
                ]),
              ) : const SizedBox(),
              SizedBox(height: Get.find<SplashController>().configModel!.scheduleOrder! ? Dimensions.paddingSizeSmall : 0),

              SwitchButtonWidget(icon: Icons.delivery_dining, title: 'delivery'.tr, isButtonActive: widget.restaurant.delivery, onTap: () {
                _restaurant.delivery = !_restaurant.delivery!;
              }),
              const SizedBox(height: Dimensions.paddingSizeSmall),

              SwitchButtonWidget(icon: Icons.flatware, title: 'cutlery'.tr, isButtonActive: widget.restaurant.cutlery, onTap: () {
                _restaurant.cutlery = !_restaurant.cutlery!;
              }),
              const SizedBox(height: Dimensions.paddingSizeSmall),

              Get.find<SplashController>().configModel!.takeAway! ? SwitchButtonWidget(icon: Icons.house_siding, title: 'take_away'.tr, isButtonActive: widget.restaurant.takeAway, onTap: () {
                _restaurant.takeAway = !_restaurant.takeAway!;
              }) : const SizedBox(),
              SizedBox(height: Get.find<SplashController>().configModel!.takeAway! ? Dimensions.paddingSizeSmall : 0),

              SwitchButtonWidget(icon: Icons.subscriptions_outlined, title: 'subscription_order'.tr, isButtonActive: widget.restaurant.orderSubscriptionActive, onTap: () {
                _restaurant.orderSubscriptionActive = !_restaurant.orderSubscriptionActive!;
              }),
              const SizedBox(height: Dimensions.paddingSizeSmall),

              Get.find<SplashController>().configModel!.instantOrder! ? Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeExtraSmall),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Row(children: [

                  const Icon(Icons.fastfood_outlined, size: 25),
                  const SizedBox(width: Dimensions.paddingSizeSmall),

                  Expanded(child: Text('instance_order'.tr, style: robotoRegular)),

                  Switch(
                    thumbIcon: thumbIcon,
                    value: restController.instantOrder,
                    onChanged: (onChanged){
                      restController.setInstantOrder(onChanged);
                    },
                  ),
                ]),
              ) : const SizedBox(),
              SizedBox(height: Get.find<SplashController>().configModel!.instantOrder! ? Dimensions.paddingSizeDefault : 0),

              Align(
                alignment: Alignment.topLeft,
                child: Text('restaurant_cover'.tr, style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeLarge)),
              ),
              const SizedBox(height: Dimensions.paddingSizeExtraSmall),

              Container(
                padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeLarge, vertical: Dimensions.paddingSizeLarge),
                decoration: BoxDecoration(
                  color: Theme.of(context).cardColor,
                  borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                  boxShadow: const [BoxShadow(color: Colors.black12, spreadRadius: 0, blurRadius: 5)],
                ),
                child: Stack(children: [

                  ClipRRect(
                    borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                    child: restController.pickedCover != null ? GetPlatform.isWeb ? Image.network(
                      restController.pickedCover!.path, width: context.width, height: 170, fit: BoxFit.cover,
                    ) : Image.file(
                      File(restController.pickedCover!.path), width: context.width, height: 170, fit: BoxFit.cover,
                    ) : CustomImageWidget(
                      image: '${widget.restaurant.coverPhotoFullUrl}',
                      height: 170, width: context.width, fit: BoxFit.cover,
                    ),
                  ),

                  Positioned(
                    bottom: 0, right: 0, top: 0, left: 0,
                    child: InkWell(
                      onTap: () => restController.pickImage(false, false),
                      child: Container(
                        decoration: BoxDecoration(
                          color: Colors.black.withValues(alpha: 0.3), borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
                          border: Border.all(width: 1, color: Theme.of(context).primaryColor),
                        ),
                        child: Container(
                          margin: const EdgeInsets.all(25),
                          decoration: BoxDecoration(
                            border: Border.all(width: 3, color: Colors.white),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.camera_alt, color: Colors.white, size: 50),
                        ),
                      ),
                    ),
                  ),

                ]),
              ),

            ]),
          )),

          SafeArea(
            child: !restController.isLoading ? Container(
              padding: const EdgeInsets.symmetric(horizontal: Dimensions.paddingSizeSmall, vertical: Dimensions.paddingSizeSmall),
              decoration: BoxDecoration(
                color: Theme.of(context).cardColor,
                boxShadow: [BoxShadow(color: Get.isDarkMode ? Colors.black.withValues(alpha: 0.2) : Colors.grey.withValues(alpha: 0.3), spreadRadius: 1, blurRadius: 10, offset: const Offset(0, 1))],
              ),
              child: CustomButtonWidget(
                margin: const EdgeInsets.all(Dimensions.paddingSizeSmall),
                onPressed: () {
                  bool defaultNameNull = false;
                  bool defaultAddressNull = false;
                  bool defaultMetaTitleNull = false;
                  bool defaultMetaDescriptionNull = false;
                  for(int index=0; index<_languageList.length; index++) {
                    if(_languageList[index].key == 'en') {
                      if (_nameController[index].text.trim().isEmpty) {
                        defaultNameNull = true;
                      }
                      if(_addressController[index].text.trim().isEmpty){
                        defaultAddressNull = true;
                      }
                      if(_metaTitleController[index].text.trim().isEmpty){
                        defaultMetaTitleNull = true;
                      }
                      if(_metaDescriptionController[index].text.trim().isEmpty){
                        defaultMetaDescriptionNull = true;
                      }
                      break;
                    }
                  }
                  String contact = _contactController.text.trim();
                  String minimumOrder = _orderAmountController.text.trim();
                  String minimumFee = _minimumChargeController.text.trim();
                  String perKmFee = _perKmChargeController.text.trim();
                  String gstCode = _gstController.text.trim();
                  String maximumFee = _maximumChargeController.text.trim();
                  String extraPackagingAmount = _extraPackagingController.text.trim();
                  String dineInAdvanceTime = _dineInAdvanceTimeController.text.trim();
                  String customOrderDate = _customerOrderDaysController.text.trim();
                  String freeDeliveryDistance = _freeDeliveryDistanceController.text.trim();

                  if(defaultNameNull) {
                    showCustomSnackBar('enter_your_restaurant_name'.tr);
                  }else if(restController.isExtraPackagingEnabled! && extraPackagingAmount.isEmpty) {
                    showCustomSnackBar('enter_restaurant_extra_packaging_charge'.tr);
                  }else if(contact.isEmpty) {
                    showCustomSnackBar('enter_restaurant_contact_number'.tr);
                  }else if(defaultAddressNull) {
                    showCustomSnackBar('enter_restaurant_address'.tr);
                  }else if(minimumOrder.isEmpty) {
                    showCustomSnackBar('enter_minimum_order_amount'.tr);
                  }else if(_restaurant.selfDeliverySystem == 1 && perKmFee.isNotEmpty && minimumFee.isEmpty /*&& maximumFee.isNotEmpty*/) {
                    showCustomSnackBar('enter_minimum_delivery_fee'.tr);
                  }else if(_restaurant.selfDeliverySystem == 1 && minimumFee.isNotEmpty && perKmFee.isEmpty /*&& maximumFee.isNotEmpty*/) {
                    showCustomSnackBar('enter_per_km_delivery_fee'.tr);
                  }else if(_restaurant.selfDeliverySystem == 1 && minimumFee.isNotEmpty && (maximumFee.isNotEmpty ? (double.parse(perKmFee) > double.parse(maximumFee)) : false) && double.parse(maximumFee) != 0) {
                    showCustomSnackBar('per_km_charge_can_not_be_more_then_maximum_charge'.tr);
                  }else if(_restaurant.selfDeliverySystem == 1 && minimumFee.isNotEmpty && (maximumFee.isNotEmpty ? (double.parse(minimumFee) > double.parse(maximumFee)) : false)) {
                    showCustomSnackBar('minimum_charge_can_not_be_more_then_maximum_charge'.tr);
                  }else if(defaultMetaTitleNull) {
                    showCustomSnackBar('enter_meta_title'.tr);
                  }else if(defaultMetaDescriptionNull) {
                    showCustomSnackBar('enter_meta_description'.tr);
                  }else if(!restController.isRestVeg! && !restController.isRestNonVeg!){
                    showCustomSnackBar('select_at_least_one_food_type'.tr);
                  }else if(restController.isGstEnabled! && gstCode.isEmpty){
                    showCustomSnackBar('enter_gst_code'.tr);
                  }else if(_restaurant.selfDeliverySystem == 1 && minimumFee.isNotEmpty && perKmFee.isNotEmpty && maximumFee.isEmpty) {
                    showCustomSnackBar('enter_maximum_delivery_fee'.tr);
                  }else {
                    List<String> cuisines = [];
                    List<Translation> translation = [];
                    List<String> restaurantCharacteristics = [];

                    for (var index in restController.selectedCuisines!) {
                      cuisines.add(restController.cuisineModel!.cuisines![index].id.toString());
                    }

                    for (var index in restController.selectedCharacteristicsList!) {
                      restaurantCharacteristics.add(index!);
                    }

                    for(int index=0; index<_languageList.length; index++) {
                      translation.add(Translation(
                        locale: _languageList[index].key, key: 'name',
                        value: _nameController[index].text.trim().isNotEmpty ? _nameController[index].text.trim()
                            : _nameController[0].text.trim(),
                      ));
                      translation.add(Translation(
                        locale: _languageList[index].key, key: 'address',
                        value: _addressController[index].text.trim().isNotEmpty ? _addressController[index].text.trim()
                            : _addressController[0].text.trim(),
                      ));
                      translation.add(Translation(
                        locale: _languageList[index].key, key: 'meta_title',
                        value: _metaTitleController[index].text.trim().isNotEmpty ? _metaTitleController[index].text.trim()
                            : _metaTitleController[0].text.trim(),
                      ));
                      translation.add(Translation(
                        locale: _languageList[index].key, key: 'meta_description',
                        value: _metaDescriptionController[index].text.trim().isNotEmpty ? _metaDescriptionController[index].text.trim()
                            : _metaDescriptionController[0].text.trim(),
                      ));
                    }

                    _restaurant.phone = contact;
                    _restaurant.minimumOrder = double.parse(minimumOrder);
                    _restaurant.gstStatus = restController.isGstEnabled;
                    _restaurant.gstCode = gstCode;
                    _restaurant.minimumShippingCharge = minimumFee.isNotEmpty ? double.parse(minimumFee) : null;
                    _restaurant.maximumShippingCharge = maximumFee.isNotEmpty ? double.parse(maximumFee) : null;
                    _restaurant.perKmShippingCharge = perKmFee.isNotEmpty ? double.parse(perKmFee) : null;
                    _restaurant.veg = restController.isRestVeg! ? 1 : 0;
                    _restaurant.nonVeg = restController.isRestNonVeg! ? 1 : 0;
                    _restaurant.instanceOrder = Get.find<RestaurantController>().instantOrder;
                    _restaurant.scheduleOrder = Get.find<RestaurantController>().scheduleOrder;
                    _restaurant.isExtraPackagingActive = restController.isExtraPackagingEnabled;
                    _restaurant.extraPackagingStatus = restController.extraPackagingSelectedValue;
                    _restaurant.extraPackagingAmount = extraPackagingAmount.isNotEmpty ? double.parse(extraPackagingAmount) : 0;
                    _restaurant.isDineInActive = restController.isDineInEnabled;
                    _restaurant.scheduleAdvanceDineInBookingDuration = dineInAdvanceTime.isNotEmpty ? int.parse(dineInAdvanceTime) : 0;
                    _restaurant.scheduleAdvanceDineInBookingDurationTimeFormat = restController.selectedTimeType;
                    _restaurant.customOrderDate = customOrderDate.isNotEmpty ? int.parse(customOrderDate) : 0;
                    _restaurant.freeDeliveryDistanceStatus = restController.freeDeliveryDistanceEnabled;
                    _restaurant.customDateOrderStatus = restController.customDateOrderEnabled;
                    _restaurant.freeDeliveryDistance = freeDeliveryDistance;

                    log('restaurant: ${_restaurant.toJson()}');

                    restController.updateRestaurant(_restaurant, cuisines, Get.find<AuthController>().getUserToken(), translation);
                  }
                },
                buttonText: 'update'.tr,
              ),
            ) : const Center(child: CircularProgressIndicator()),
          ),

        ]);
      }),
    );
  }
}