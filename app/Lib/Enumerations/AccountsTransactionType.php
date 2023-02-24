<?php
namespace App\Lib\Enumerations;

class AccountsTransactionType
{
    public static $journal = 1;
    public static $contra = 2; //Contra Voucher
    public static $cashPayment = 3;
    public static $bankPayment = 4; //Bank Payment Voucher
    public static $cashReceipt = 5;
    public static $bankReceipt = 6; //Bank Receipt Voucher
    public static $bankTransfer = 7; //Bank Transfer/Deposit

    public static $salesInvVoucer = 8; //Sales Voucher
    public static $salesInvVoucerTitle = 'Sales Voucher';
    public static $salesInvRtnVoucer = 9; //Sales Return Voucher
    public static $salesInvRtnVoucerTitle = 'Sales Return Voucher';
    public static $purchaseInvVoucer = 10;
    public static $purchaseInvVoucerTitle = 'Purchase Voucher';
    public static $purchaseInvRtnVoucer = 11;
    public static $purchaseInvRtnVoucerTitle = 'Purchase Return Voucher';

    public static $purchaseCostEntry = 15;

    public static $lcBankCharge = 68;
    public static $lcBankChargeTitle = 'LC Bank Charge';

    public static $lcInsuranceCharge = 69;
    public static $lcInsuranceChargeTitle = 'LC Insurance Charge';

    public static $lcOpeningCharge = 70;
    public static $lcOpeningChargeTitle = 'LC Opening Charge';
    public static $lcMarginValue = 71;
    public static $lcMarginValueTitle = 'LC Margin';
    public static $lcLatrValue = 72;
    public static $lcLatrTitle = 'LC LATR Payment';
    public static $lcCustomDutyCharge = 73;
    public static $lcCustomDutyChargeTitle = 'LC Custom Duty Charge';
    public static $lcOtherCharge = 74;
    public static $lcOtherChargeTitle = 'LC Other Charge';

    public static $lcStockEntry = 75;
    public static $lcStockEntryTitle = 'LC Stock Entry';
    public static $lcClosingEntry = 76;
    public static $lcClosingEntryTitle = 'LC Closing';


    public static $salarySummeryEntry = 101;
    public static $salarySummeryEntryTitle = 'Salary Summery';

    public static $bonusSummeryEntry = 201;
    public static $bonusSummeryEntryTitle = 'Bonus Summery Process Entry';



    public static $ProductionReqRm = 250;
    public static $ProductionReqRmTitle = 'Production Req fom RM';

    public static $ProductionMassbody = 251;
    public static $ProductionMassbodyTitle = 'Production Massbody';

    public static $ProductionForming = 252;
    public static $ProductionFormingTitle = 'Production Forming';

    public static $ProductionShapping = 253;
    public static $ProductionShappingTitle = 'Production Shapping';

    public static $ProductionDryer = 254;
    public static $ProductionDryerTitle = 'Production Dryer';

    public static $ProductionGlaze = 255;
    public static $ProductionGlazeTitle = 'Production Glaze';

    public static $ProductionKiln = 256;
    public static $ProductionKilnTitle = 'Production Kiln';

    public static $ProductionTesting = 257;
    public static $ProductionTestingTitle = 'Production Testing';

	public static $ProductiontesTiting = 21;
    public static $ProductiontesTitingTitle = 'Production Testing or HV';


    public static $ProductionFinished = 258;
    public static $ProductionFinishedTitle = 'Production Finished';

	public static $ProductionAssemble = 22;
    public static $ProductionAssembleTitle = 'Production Assemble';

	public static $ProductionPacking = 23;
    public static $ProductionPackingTitle = 'Production Packing';
}
