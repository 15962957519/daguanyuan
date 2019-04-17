package controllers

import (
	"encoding/json"
	"github.com/astaxie/beego"
)

// Operations about Product
type GoodsController struct {
	beego.Controller
}
// @Title GetAll
// @Description get all Users
// @Success 200 {object} models.User
// @router / [get]
func (g *GoodsController) GetAll() {

}
