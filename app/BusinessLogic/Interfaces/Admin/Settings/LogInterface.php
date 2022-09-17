<?php

namespace App\BusinessLogic\Interfaces\Admin\Settings;

/**
 * LogInterface is a contract for what methods will be used in the ContactMeService class.
 * This consists of the following CRUD operations methods:
 * - handleIndex();
 * - handleShow();
 */
interface LogInterface
{
    /**
     * This function is specific for displaying all the resources from the database by handling all the HTTP requests [ GET ].
     * 
     * @return \Illuminate\Http\Response
     */
    public function handleIndex();

    /**
     * This function is specific for displaying a single resource from the database by handling all the HTTP request [ GET ].
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function handleShow($id);
}
