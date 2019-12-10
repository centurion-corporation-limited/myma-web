<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Singx extends Model
{
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "transactions_singx";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'accountNumber', 'isInvoiceRequired', 'transactionDT', 'userTxnId', 'primaryKey', 'transactionId','accountMapId', 'activityId',
        'contactId', 'customerId', 'enquiryId', 'reasonId', 'receiverBankId','receiverCountryId', 'receiverId', 'singxAccountId', 'stageId',
        'statusId', 'lroStatusId', 'lroResponse', 'statusUpdateOn', 'responseReceived', 'transactionResponse', 'mTradeTxnId','receiverRelationship',
        'promoCode','txnreference','receiverName','bankName','countryName','firstName','lastName','response','status','singx_fee', 'myma_part',
        'singx_part', 'sent_amount','received_amount','exchange_rate','transaction_amount', 'gst'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
