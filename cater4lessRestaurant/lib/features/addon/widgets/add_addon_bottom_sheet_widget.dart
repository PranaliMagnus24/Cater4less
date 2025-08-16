import 'package:flutter/foundation.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_dropdown_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_text_form_field_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/models/config_model.dart';
import 'package:stackfood_multivendor_restaurant/features/restaurant/controllers/restaurant_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/restaurant/domain/models/product_model.dart';
import 'package:stackfood_multivendor_restaurant/features/addon/controllers/addon_controller.dart';
import 'package:stackfood_multivendor_restaurant/features/splash/controllers/splash_controller.dart';
import 'package:stackfood_multivendor_restaurant/helper/custom_print_helper.dart';
import 'package:stackfood_multivendor_restaurant/util/dimensions.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_button_widget.dart';
import 'package:stackfood_multivendor_restaurant/common/widgets/custom_snackbar_widget.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:stackfood_multivendor_restaurant/util/styles.dart';

class AddAddonBottomSheetWidget extends StatefulWidget {
  final AddOns? addon;
  const AddAddonBottomSheetWidget({super.key, required this.addon});

  @override
  State<AddAddonBottomSheetWidget> createState() => _AddAddonBottomSheetWidgetState();
}

class _AddAddonBottomSheetWidgetState extends State<AddAddonBottomSheetWidget> with TickerProviderStateMixin {

  final List<TextEditingController> _nameControllers = [];
  final TextEditingController _priceController = TextEditingController();
  final TextEditingController _stockTextController = TextEditingController();
  final List<FocusNode> _nameNodes = [];
  final FocusNode _priceNode = FocusNode();
  final List<Language>? _languageList = Get.find<SplashController>().configModel!.language;
  TabController? _tabController;
  final List<Tab> _tabs =[];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _languageList!.length, initialIndex: 0, vsync: this);
    for (var language in _languageList) {
      if (kDebugMode) {
        print(language);
      }
      _nameControllers.add(TextEditingController());
      _nameNodes.add(FocusNode());
    }

    if(widget.addon != null) {
      for(int index=0; index<_languageList.length; index++) {
        _nameControllers.add(TextEditingController(text: widget.addon!.translations![widget.addon!.translations!.length-1].value));
        _nameNodes.add(FocusNode());
        for(Translation translation in widget.addon!.translations!) {
          if(_languageList[index].key == translation.locale && translation.key == 'name') {
            _nameControllers[index] = TextEditingController(text: translation.value);
            break;
          }
        }
      }
      _priceController.text = widget.addon!.price.toString();
    }else {
      for (var language in _languageList) {
        _nameControllers.add(TextEditingController());
        _nameNodes.add(FocusNode());
        customPrint(language);
      }
    }

    for (var language in _languageList) {
      _tabs.add(Tab(text: language.value));
    }

    _stockTextController.text = widget.addon?.addonStock == 0 ? '' : widget.addon?.addonStock!.toString() ?? '';
    _setStockType(widget.addon?.stockType);

  }

  void _setStockType(String? type) {
    if(type == 'limited') {
      Get.find<RestaurantController>().setStockTypeIndex(1, false);
    } else if (type == 'daily') {
      Get.find<RestaurantController>().setStockTypeIndex(2, false);
    } else {
      Get.find<RestaurantController>().setStockTypeIndex(0, false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<RestaurantController>(builder: (restaurantController) {

      List<DropdownItem<int>> stockTypeList = _generateStockTypeList(restaurantController.stockTypeList, context);

      return Container(
        height: MediaQuery.of(context).size.height * 0.50,
        padding: const EdgeInsets.all(Dimensions.paddingSizeDefault),
        decoration: BoxDecoration(
          color: Theme.of(context).cardColor,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(Dimensions.radiusLarge)),
        ),
        child: SingleChildScrollView(child: Column(mainAxisSize: MainAxisSize.min, children: [

          Container(
            height: 5, width: 50,
            decoration: BoxDecoration(
              color: Theme.of(context).disabledColor.withValues(alpha: 0.3),
              borderRadius: BorderRadius.circular(Dimensions.radiusSmall),
            ),
          ),
          const SizedBox(height: Dimensions.paddingSizeLarge),

          SizedBox(
            height: 40,
            child: TabBar(
              tabAlignment: TabAlignment.start,
              controller: _tabController,
              indicatorColor: Theme.of(context).primaryColor,
              indicatorWeight: 3,
              labelColor: Theme.of(context).primaryColor,
              unselectedLabelColor: Theme.of(context).disabledColor,
              unselectedLabelStyle: robotoRegular.copyWith(color: Theme.of(context).disabledColor, fontSize: Dimensions.fontSizeSmall),
              labelStyle: robotoBold.copyWith(fontSize: Dimensions.fontSizeDefault, color: Theme.of(context).primaryColor),
              labelPadding: const EdgeInsets.only(right: Dimensions.radiusDefault),
              isScrollable: true,
              indicatorSize: TabBarIndicatorSize.tab,
              tabs: _tabs,
              onTap: (int ? value) {
                setState(() {});
              },
            ),
          ),
          const Padding(
            padding: EdgeInsets.only(bottom: Dimensions.paddingSizeLarge),
            child: Divider(height: 0),
          ),

          CustomTextFormFieldWidget(
            //hintText: '${'addon_name'.tr} (${_languageList?[_tabController!.index].value})',
            hintText: 'name'.tr,
            controller: _nameControllers[_tabController!.index],
            focusNode: _nameNodes[_tabController!.index],
            nextFocus: _tabController!.index != _languageList!.length-1 ? _priceNode : _priceNode,
            inputType: TextInputType.name,
            capitalization: TextCapitalization.words,
            showTitle: false,
          ),
          const SizedBox(height: 35),

          CustomTextFormFieldWidget(
            hintText: 'price'.tr,
            controller: _priceController,
            focusNode: _priceNode,
            inputAction: TextInputAction.done,
            inputType: TextInputType.number,
            isAmount: true,
            showTitle: false,
          ),
          const SizedBox(height: 35),

          Row(children: [

            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

              Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(Dimensions.radiusDefault),
                  color: Theme.of(context).cardColor,
                  border: Border.all(color: Theme.of(context).disabledColor, width: 0.3),
                ),
                child: CustomDropdownWidget<int>(
                  onChange: (int? value, int index) {
                    restaurantController.setStockTypeIndex(index, true);
                    _stockTextController.text = '';
                  },
                  dropdownButtonStyle: DropdownButtonStyle(
                    height: 50,
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
                  items: stockTypeList,
                  child: Text(
                    restaurantController.stockTypeList[restaurantController.stockTypeIndex!]!.tr,
                    style: robotoRegular.copyWith(color: Theme.of(context).textTheme.bodyMedium!.color),
                  ),
                ),
              )

            ])),
            const SizedBox(width: Dimensions.paddingSizeDefault),

            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

              CustomTextFormFieldWidget(
                hintText: restaurantController.stockTypeIndex == 0 ? 'unlimited'.tr : 'addon_stock'.tr,
                controller: _stockTextController,
                inputAction: TextInputAction.done,
                inputType: TextInputType.phone,
                showTitle: false,
                readOnly: restaurantController.stockTypeIndex == 0 || (restaurantController.stockTextFieldDisable) ? true : false,
              ),

            ])),

          ]),
          const SizedBox(height: 50),

          GetBuilder<AddonController>(builder: (addonController) {
            return !addonController.isLoading ? CustomButtonWidget(
              onPressed: () {

                String name = _nameControllers[0].text.trim();
                String price = _priceController.text.trim();

                int addonStock = 0;
                try{
                  addonStock = int.parse(_stockTextController.text.trim());
                } catch(e) {
                  addonStock = 0;
                }

                if(name.isEmpty) {
                  showCustomSnackBar('enter_addon_name'.tr);
                }else if(price.isEmpty) {
                  showCustomSnackBar('enter_addon_price'.tr);
                }else if(_stockTextController.text.isEmpty && restaurantController.stockTypeIndex != 0){
                  showCustomSnackBar('enter_the_addon_stock'.tr);
                }else {
                  List<Translation> nameList = [];
                  for(int index=0; index<_languageList.length; index++) {
                    nameList.add(Translation(
                      locale: _languageList[index].key, key: 'name',
                      value: _nameControllers[index].text.trim().isNotEmpty ? _nameControllers[index].text.trim()
                          : _nameControllers[0].text.trim(),
                    ));
                  }

                  AddOns addon = AddOns(
                    name: name, price: double.parse(price), translations: nameList,
                    addonStock: addonStock == 0 ? null : addonStock,
                    stockType: restaurantController.stockTypeIndex == 0 ? 'unlimited' : restaurantController.stockTypeIndex == 1 ? 'limited' : 'daily',
                  );

                  if(widget.addon != null) {
                    addon.id = widget.addon!.id;
                    addonController.updateAddon(addon);
                  }else {
                    addonController.addAddon(addon);
                  }
                }
              },
              buttonText: widget.addon != null ? 'update'.tr : 'submit'.tr,
            ) : const Center(child: CircularProgressIndicator());
          }),

        ])),
      );
    });
  }

  List<DropdownItem<int>> _generateStockTypeList(List<String?> typeList, BuildContext context) {
    List<DropdownItem<int>> generateDmTypeList = [];
    for(int index=0; index<typeList.length; index++) {
      generateDmTypeList.add(DropdownItem<int>(value: index, child: SizedBox(
        child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Text(
            '${typeList[index]?.tr}',
            style: robotoRegular.copyWith(color: Theme.of(context).textTheme.bodyMedium!.color),
          ),
        ),
      )));
    }
    return generateDmTypeList;
  }

}