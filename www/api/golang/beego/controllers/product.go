package controllers

import (
	"apiproject/models"
	"encoding/json"
	"github.com/astaxie/beego"
)

// Operations about Product
type ProductController struct {
	beego.Controller
}


// @Title GetAll
// @Description get all Users
// @Success 200 {object} models.User
// @router / [get]
func (u *ProductController) GetAll() {
	users := models.GetAllUsers()
	u.Data["json"] = users
	u.ServeJSON()
}

// @Title Get
// @Description get user by uid
// @Param	uid		path 	string	true		"The key for staticblock"
// @Success 200 {object} models.User
// @Failure 403 :uid is empty
// @router /:uid [get]
func (u *ProductController) Get() {
	uid := u.GetString(":uid")
	if uid != "" {
		user, err := models.GetUser(uid)
		if err != nil {
			u.Data["json"] = err.Error()
		} else {
			u.Data["json"] = user
		}
	}
	u.ServeJSON()
}
