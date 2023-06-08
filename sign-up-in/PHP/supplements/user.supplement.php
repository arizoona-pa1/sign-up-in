<?php
const Error_text_user = [
    "invalid" => ""
];
class user extends authentication
{
    public readonly ?string $name;
    public readonly ?string $email;
    public readonly ?string $fname;
    public readonly ?string $lname;
    public readonly ?string $gender;
    public readonly array|object|null $languages;
    public readonly ?string $img;
    public readonly ?string $country;
    public readonly ?string $city;
    public readonly ?string $AddresLine;
    public readonly ?string $amount;
    public readonly ?string $currency;
    public readonly ?string $messages;
    function __construct(string $SECURE_B, string $SECURE_A, string $ID)
    {
        parent::__construct(
            $SECURE_B,
            $SECURE_A,
            $ID
        );
        if ($this->accessAllowed) {
        
            $MySQL = new query();
            $MySQL->read("users", "ID = '$ID'");
            $user = $MySQL->fetch ?? null ;
            $this->email = $user->email ?? null ;
            $MySQL->read("personal_info", "IDUser = '$ID'");
            $persona_info = $MySQL->fetch ?? null;
            $this->fname = $persona_info->firstName ?? null;
            $this->lname = $persona_info->lastName ?? null ;
            $this->gender = $persona_info->gender ?? null ;
            $this->languages = $persona_info->Languages ?? null;
            $this->img = $persona_info->Images[$persona_info->imageSelected] ?? null;
            $MySQL->read("address", "IDUser = '$ID'");
            $address = $MySQL->fetch ?? null ;
            $this->city = $address->City ?? null ;
            $this->country = $address->Country ?? null ;
            $this->AddresLine = $address->AddresLine1 ?? null ;
            $MySQL->read("wallet", "IDUser = '$ID'");
            $wallet = $MySQL->fetch ?? null ;
            $this->amount = $wallet->amount ?? null ;
            $this->currency = $wallet->currency ?? null ;
        } else {
            $this->messages = Error_text_user["invalid"];
        }
    }

}
?>