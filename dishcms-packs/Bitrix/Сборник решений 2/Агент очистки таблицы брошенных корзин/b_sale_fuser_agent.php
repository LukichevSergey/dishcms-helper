<?php
use Bitrix\Main;
use Bitrix\Sale\Fuser;

/*
\CAgent::AddAgent(
    "Kontur_B_Sale_FUser_Agent::deleteOldAgent(10);",  // имя функции
    "sale",                // идентификатор модуля
    "N",                      // агент не критичен к кол-ву запусков
    86400,                    // интервал запуска - 1 сутки
    "",                       // дата первой проверки - текущее
    "Y",                      // агент активен
    "",                       // дата первого запуска - текущее
	30
);
*/

class Kontur_B_Sale_FUser_Agent
{
	const BASKET_DELETE_LIMIT=20000000;
	
	public static function deleteOld($days)
    {
            $expired = new Main\Type\DateTime();
            $expired->add('-'.$days.' days');
            $expiredValue = $expired->format('Y-m-d H:i:s');

           	/** @var Main\DB\Connection $connection */
            $connection = Main\Application::getConnection();
           	/** @var Main\DB\SqlHelper $sqlHelper */
            $sqlHelper = $connection->getSqlHelper();

            $sqlExpiredDate = $sqlHelper->getDateToCharFunction("'" . $expiredValue . "'");

            $query = "DELETE FROM b_sale_basket     WHERE
                   	FUSER_ID IN (
                           	SELECT b_sale_fuser.id FROM b_sale_fuser WHERE
                                           	b_sale_fuser.DATE_UPDATE < ".$sqlExpiredDate."
                                           	AND b_sale_fuser.USER_ID IS NULL
                           	) AND ORDER_ID IS NULL LIMIT ". static::BASKET_DELETE_LIMIT;

           	$connection->queryExecute($query);
            $affectRows = $connection->getAffectedRowsCount();

            $query = "DELETE FROM b_sale_basket
                    WHERE
                            FUSER_ID NOT IN (SELECT b_sale_fuser.id FROM b_sale_fuser)
                            AND
                            ORDER_ID IS NULL
                    LIMIT ". static::BASKET_DELETE_LIMIT;

            $connection->queryExecute($query);
            $affectRows = max($affectRows, $connection->getAffectedRowsCount());

            $query = "
                    DELETE
                    FROM b_sale_basket_props
                    WHERE b_sale_basket_props.BASKET_ID NOT IN (
                            SELECT b_sale_basket.ID FROM b_sale_basket
                    )
                    LIMIT ".static::BASKET_DELETE_LIMIT;

            $connection->queryExecute($query);

            return max($affectRows, $connection->getAffectedRowsCount());
    }
    
            /**
         * Delete fuserId over several days.
         *
         * @param int $days                     Interval.
         * @return void
         */
        public static function deleteOldFUser($days)
        {
                $expired = new Main\Type\DateTime();
                $expired->add('-'.$days.' days');
                $expiredValue = $expired->format('Y-m-d H:i:s');

                /** @var Main\DB\Connection $connection */
                $connection = Main\Application::getConnection();
                /** @var Main\DB\SqlHelper $sqlHelper */
                $sqlHelper = $connection->getSqlHelper();

                $query = "DELETE FROM b_sale_fuser WHERE
                                                                       	b_sale_fuser.DATE_UPDATE < ".$sqlHelper->getDateToCharFunction("'".$expiredValue."'")."
                                                                       	AND b_sale_fuser.USER_ID IS NULL
                                                                       	AND b_sale_fuser.id NOT IN (select FUSER_ID from b_sale_basket)";
                $connection->queryExecute($query);
        }


	/**
     * @param $days
     * @param int $speed
     * @return string
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
	public static function deleteOldAgent($days, $speed = 0)
    {
            if (!isset($GLOBALS["USER"]) || !is_object($GLOBALS["USER"]))
            {
                    $tmpUser = True;
                    $GLOBALS["USER"] = new \CUser();
            }

            $affectRows = static::deleteOld($days);
            static::deleteOldFUser($days);

            $days = intval(Main\Config\Option::get("sale", "delete_after", "30"));
            $result = "\Kontur_B_Sale_FUser_Agent::deleteOldAgent(".$days.");";

            if ($affectRows === static::BASKET_DELETE_LIMIT)
            {
                    global $pPERIOD;
                    $pPERIOD = 300;
            }

            if (isset($tmpUser))
            {
                    unset($GLOBALS["USER"]);
            }

            return $result;
    }
}
