<?php
/**
 * This is a skeleton for other modules to call. Simple, just puts all the
 * settings in a nice form.
 */
class ControllerExtensionModuleDevPatches extends Controller {
    private $error = array();
    private function getUserTokenQString() { return 'user_token=' . $this->session->data['user_token']; }
    // Implement these on your child module controller class.
    // They have to be public
    public function getSettingCode() { return 'dev_patches'; }
    public function getPermissionName() { return 'extension/module/dev_patches'; }
    public function getRoute() { return 'extension/module/dev_patches'; }
    public function getDefaultSettings() { return array(
        "name" => array("type" => "text", "lang_key" => "name", "default" => "Dev Patches"),
        "status" => array("type" => "boolean", "lang_key" => "status", "disabled" => true, "default" => "1"), 
    );}

    // Cache of status
    public static $enabled = null;
    public function isEnabled() {
        if (ControllerExtensionModuleDevPatches::$enabled === null) {
            $this->load->model('setting/setting');
            $status = $this->model_setting_setting->getSettingValue($this->getSettingKey("status"));
            ControllerExtensionModuleDevPatches::$enabled = $status === "1";
        }
        return ControllerExtensionModuleDevPatches::$enabled;
    }

    public function getSettingKey(string $key) {
        return $this->getSettingCode() . ":" . $key;
    }
    public function removeSettingCodeFromKey(string $key) {
        return substr($key, strpos($key, ":") + 1);
    }

    public function getUserTokenPatchStr() {
        return "try {\$this->load->library(\"extension/dev_patches_request\");} catch (\Throwable \$th) {}";
    }
    public function getUserTokenPatchSearch() {
        if (version_compare(VERSION, "4.0.0", ">=")) {
            return "public function index(): object|null {";
        }
        return "public function index() {";
    }
    public function getUserTokenPatchPath() {
        return DIR_APPLICATION . "controller/startup/login.php";
    }
    /**
     * Basic function of the controller. This can be called using route=extension/module/dev_patches
     */
    public function index() {
        // Load all language variables
        $language_vars = $this->load->language('extension/module/dev_patches');
        $data = array();
        $data = array_merge($data, $language_vars);
        $this->document->setTitle($this->language->get('heading_title'));
        // Load the module settings file so we can actually work with separate
        // instances of this module...? I don't know if that's the use case but
        // it's definitely possible.
        $this->load->model('setting/setting');
        /**
         * Checks whether the request type is post. If yes, then calls the validate function.
         */
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($this->getPermissionName())) {
            /**
             * The function named 'editSetting' of a model is called in this way
             * The first argument is the code of the module and the second argument contains all the post values
             * The code must be same as your file name
             */
            $new_settings = $this->request->post;
            // Check if the extension was disabled/enabled and apply patches accordingly
            $old_status = $this->model_setting_setting->getSettingValue($this->getSettingKey("status"));
            if ($new_settings["status"] === "0" && $old_status === "1") {
                // Remove patches
                $this->removeUserTokenPatch();
            } else if ($new_settings["status"] === "1" && $old_status === "0") {
                // Add patches back
                $this->addUserTokenPatch();
            }
            // The post values must be turned into OpenCart compatible simple list
            $save_data = array();
            foreach ($new_settings as $key => $value) {
                $save_data[$this->getSettingKey($key)] = $value;
            }
            $this->model_setting_setting->editSetting($this->getSettingCode(), $save_data);
            /**
             * The success message is kept in the session
             */
            $this->session->data['success'] = $this->language->get('text_success');
            /**
             * The redirection works in this way.
             * After insertion of data, it will redirect to extension/module file along with the token
             * The success message will be shown there
             */
            $this->response->redirect($this->url->link('marketplace/extension', $this->getUserTokenQString() . '&type=module', true));
        }
        /**
         * If there is any warning in the private property '$error', then it will be put into '$data' array
         */
        $data['error_warning'] = '';
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }
        /**
         * Breadcrumbs leading to this edit page
         */
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->getUserTokenQString(), true)
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', $this->getUserTokenQString() . '&type=module', true)
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->getRoute(), $this->getUserTokenQString(), true)
        );
        $data['breadcrumbs'] = $breadcrumbs;
        /**
         * Form action url is created and defined to $data['action']
         */
        $data['action'] = $this->url->link($this->getRoute(), $this->getUserTokenQString(), true);
        /**
         * Cancel/back button url which will lead you to module list
         */
        $data['cancel'] = $this->url->link('marketplace/extension', $this->getUserTokenQString() . '&type=module', true);
        // This will be used in the form
        $settings = $this->getDefaultSettings();
        // Fetch the settings
        $raw_saved_settings = $this->model_setting_setting->getSetting($this->getSettingCode());
        $saved_settings = array();
        // Keys are stored like "setting_code:setting_key" so they need to be
        // de-prefixed
        foreach ($raw_saved_settings as $key => $value) {
            $saved_settings[$this->removeSettingCodeFromKey($key)] = $value;
        }
        // Free variable space
        unset($raw_saved_settings);
        foreach ($settings as $key => $options) {
            $value = $settings[$key]['default'];
            if (isset($saved_settings[$key])) {
                $value = $saved_settings[$key];
            }
            if (isset($this->request->post[$key])) {
                // If a post request was sent then the value is from the post request
                $value = $this->request->post[$key];
            }
            $settings[$key]["value"] = $value;
            $settings[$key]["title"] = $language_vars["form_" . $options["lang_key"]];
            $possible_sub_title = "form_" . $options["lang_key"] . "_sub";
            $settings[$key]["title_sub"] = isset($language_vars[$possible_sub_title]) ? $language_vars[$possible_sub_title] : "";
            $possible_hint = "form_" . $options["lang_key"] . "_hint";
            $settings[$key]["title_hint"] = isset($language_vars[$possible_hint]) ? $language_vars[$possible_hint] : "";
        }
        $data["settings"] = $settings;
        // Load the default layout
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $output = $this->load->view('extension/module/dev_patches', $data);
        $this->response->setOutput($output);
    }
    /**
     * validate function validates the values of the post and also the permission
     * @return boolean return true if any of the index of $error contains value
     */
    protected function validate() {
        $perm = $this->getPermissionName();
        /**
         * Check whether the current user has the permissions to modify the settings of the module
         * The permissions are set in System->Users->User groups
         */
        if (!$this->user->hasPermission('modify', $perm)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }
        return true;
    }

    public function install() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting($this->getSettingCode(), array(
            $this->getSettingKey("name") => "Dev Patches",
            $this->getSettingKey("status") => 1,
        ));
        // Add events to the product design tab
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode(
            $this->getSettingCode()
        );
        $this->model_setting_event->addEvent(
            $this->getSettingCode(), 
            'admin/controller/common/header/after', 
            'extension/module/dev_patches/addDevModeBanner'
        );
        $this->model_setting_event->addEvent(
            $this->getSettingCode(), 
            'admin/controller/extension/extension/promotion/after', 
            'extension/module/dev_patches/removePayPalAd'
        );
        // Remove direct patches
        $this->removeUserTokenPatch();
        // Add patches
        $this->addUserTokenPatch();
    }

    public function uninstall() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode(
            $this->getSettingCode()
        );
        // Remote the direct patches
        $this->removeUserTokenPatch();
    }

    public function isUserTokenPatched(&$original_file) {
        $search = $this->getUserTokenPatchStr();
        $path   = $this->getUserTokenPatchPath();
        $original_file = $original_file ?? file_get_contents($path);

        $pos = strpos($original_file, $search);
        return $pos !== false;
    }

    public function addUserTokenPatch() {
        $patch  = $this->getUserTokenPatchStr();
        $path   = $this->getUserTokenPatchPath();
        $search = $this->getUserTokenPatchSearch();
        $original_file = file_get_contents($path);

        // Skip if patch is already applied
        if ($this->isUserTokenPatched($original_file)) {
            return false;
        }
        
        $pos = strpos($original_file, $search);
        if ($pos === false) {
            error_log( "[dev_patches]: Could not apply patch, file did not contain expected contents!\n"
                . "Path: " . $path . " Expected: `" . $search . "`"
            );
            return false;
        }

        $insert_pos = $pos + strlen($search);
        $file = $this->slice($original_file, $insert_pos, $insert_pos, $patch);
        return file_put_contents($path, $file) !== false;
    }

    public function removeUserTokenPatch() {
        $path = $this->getUserTokenPatchPath();
        $search = $this->getUserTokenPatchStr();
        $original_file = file_get_contents($path);
        
        $start_pos = strpos($original_file, $search);
        if ($start_pos === false) {
            error_log( "[dev_patches]: Could not remove patch, file did not contain expected contents!\n"
                . "Path: " . $path . " Expected: `" . $search . "`"
            );
            return false;
        }

        $end_pos = $start_pos + strlen($search);
        $file = $this->slice($original_file, $start_pos, $end_pos, "");
        return file_put_contents($path, $file) === false;
    }
    
    public function addDevModeBanner(&$route, &$args, &$output) {
        // Check if the module is disabled
        if (!$this->isEnabled()) return;
        $pos = $this->findHeaderPos($output);
        if ($pos === false) return;
        // Insert the tab
        $output = $this->slice($output, $pos, $pos, $this->load->view('extension/module/dev_patches/banner', array()));
    }
    
    private function findHeaderPos(&$output, $offset = 0) {
        // How the html looks like
        $lookup = "<header id=\"header\" class=\"navbar navbar-static-top\">";
        $close = "</header>";
        // Find a line that matches the lookup
        $start = strpos($output, $lookup, $offset);
        $end = strpos($output, $close, $start);
        if ($start === false || $end === false) {
            // If no start or end was found then for some reason the category 
            // name input cannot exist
            return false;
        }
        // Return the insert position for the banner
        return $end;
    }

    public function removePayPalAd(&$route, &$args, &$output) {
        // Check if the module is disabled
        if (!$this->isEnabled()) return;
        $output = "";
    }

    private function slice(&$output, &$start, &$end, $insert) {
        $pre = substr($output, 0, $start);
        $post = substr($output, $end);
        return $pre . $insert . $post;
    }
}
