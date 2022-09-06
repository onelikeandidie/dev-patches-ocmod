<?php
namespace Extension;
class Dev_Patches_Request {
    public $exists;
	public function __construct($registry) {
        $request = $registry->get("request");
        $request->get["user_token"] = $registry->get("session")->data["user_token"];
        $registry->set("request", $request);
        $this->exists = true;
    }
}