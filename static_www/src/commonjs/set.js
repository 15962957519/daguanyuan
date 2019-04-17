/**
 * Created by Administrator on 2018/7/20 0020.
 */
var Setwepai = function(){
    var items={};
    this.has=function(value){
        //return value in items;
        return this.hasOwnProperty(value);
    }
    this.add=function (value) {
        if(!this.has(value)){
            items[value]=value;
            return true;
        }
        return false;
    }
    this.remove=function(value){
        if(this.has(value)){
            delete items[value];
            return true;
        }
        return false;
    }
    this.clear=function(){
        items={};
    }
    this.size=function(){
        return Object.keys(items).length;
    }
    this.sizeLegacy=function(){
        var count=0;
        for (var prop in items) {
            if (items.hasOwnProperty(prop)) {
                ++count;
            }
        }
    }
    this.values=function () {
        return Object.keys(items);
    }
    this.valuesLagacy=function () {
        var keys=[];
        for (var key in items) {
            if (items.hasOwnProperty(key)) {
                keys.push(key);
            }
        }
        return keys;
    }
    //并集
    this.union=function(otherSet) {
        var unionSet=new Set();
        var values=this.values();
        for (var i = 0; i < values.length; i++) {
            unionSet.add(values[i]);
        }
        values=otherSet.values();
        for (var i = 0 ;i < values.length; i++) {
            unionSet.add(values[i]);
        }
        return unionSet;
    }
    // 交集
    this.intersection=function (otherSet) {
        var intersectionSet=new Set();
        var values=this.values();
        for (var i = 0; i < values.length; i++) {
            if (otherSet.has(values[i])) {
                intersectionSet.add(values[i])
            }
        }
        return intersectionSet;
    }
    // 差集
    this.difference=function(otherSet){
        var differenceSet=new Set();
        var values=this.values();
        for (var i = 0; i < values.length; i++) {
            if (!otherSet.has(values[i])) {
                differenceSet.add(values[i]);
            }
        }
        return differenceSet;
    }
    // 子集
    this.isSubsetOf=function(otherSet){
        if(this.size()>otherSet.size()){
            return false;
        }else{
            var values=this.values();
            for (var i = 0; i < values.length; i++) {
                if(!otherSet.has(values[i])){
                    return false;
                }
            }
            return true;
        }
    }
}

export default  Setwepai;