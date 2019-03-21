        function assignPicExec(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = ($("#pickemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'assignPic';
                        if (pickempid == '')
                        {
                            alert("Please select Pick-up Executive to assign.");
                        } else
                        {
                            $.ajax({
                                type: "POST",
                                url: "trackerupdate",
                                data:
                                    {
                                        data: pickempid,
                                        order: orderid,
                                        flag: updateFlag
                                    },
                                success: function (msg)
                                {
                                    if (msg == 'success')
                                    {
                                        $("#pickemp" + ord).attr("disabled", true);
                                        $("#assignPic" + ord).attr("disabled", true);
                                        $("#altermsg").css("color", "green");
                                        $("#altermsg").text(ord + " Assigned Executive successfully");
                                        $("#pick" + ord).attr("disabled", false);
                                    } else
                                    {
                                        $("#altermsg").css("color", "red");
                                        $("#altermsg").text("Unable to update status");
                                    }
                                }
                            });
                        }
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function PickUpdate(ord)
        {
            if (document.getElementById("pickemp" + ord).value == document.getElementById("usercode").value)
            {
                //
                var pickempid = ($("#pickemp" + ord).val());
                var orderid = ord;
                var updateFlag = 'Pick';
                $.ajax({
                    type: "POST",
                    url: "trackerupdate",
                    data:
                        {
                            data: pickempid,
                            order: orderid,
                            flag: updateFlag
                        },
                    success: function (msg)
                    {
                        if (msg == 'success')
                        {
                            $("#pick" + ord).css("background-color", "green");
                            $("#pick" + ord).attr("disabled", true);
                            $("#altermsg").css("color", "green");
                            $("#altermsg").text(ord + " Pick successful");
                            $("#dp1" + ord).attr("disabled", false);
                        } else
                        {
                            $("#altermsg").css("color", "red");
                            $("#altermsg").text("Unable to update status");
                        }
                    }
                });
                //
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        function DP1Update(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = ($("#pickemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'DP1';
                        $.ajax({
                            type: "POST",
                            url: "trackerupdate",
                            data:
                                {
                                    data: pickempid,
                                    order: orderid,
                                    flag: updateFlag
                                },
                            success: function (msg)
                            {
                                if (msg == 'success')
                                {
                                    $("#dp1" + ord).css("background-color", "green");
                                    $("#dp1" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " DP1 successful");
                                    $("#Shtl" + ord).attr("disabled", false);
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function ShtlUpdate(ord)
        {
            var val = '';
            var flags = 'shuttle';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val,
                    flag: flags
                },
                success: function (response)
                {
                    //alert(response);
                    if (response == 'shuttleTrue')
                    {
                        //
                        var pickempid = ($("#pickemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'Shtl';
                        $.ajax({
                            type: "POST",
                            url: "trackerupdate",
                            data:
                                {
                                    data: pickempid,
                                    order: orderid,
                                    flag: updateFlag
                                },
                            success: function (msg)
                            {
                                //alert(msg);
                                if (msg == 'success')
                                {
                                    $("#Shtl" + ord).css("background-color", "green");
                                    $("#Shtl" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " Shtl successful");
                                    $("#DP2" + ord).attr("disabled", false);
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function cp1Update(ord)
        {
            var val = '';
            var flags = 'shuttle';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val,
                    flag: flags
                },
                success: function (response)
                {
                    //alert(response);
                    if (response == 'shuttleTrue')
                    {
                        var pickempid = 'na';
                        var orderid = ord;
                        var updateFlag = 'cp1';
                        $.ajax(
                        {
                            type: 'post',
                            url: 'trackerupdate',
                            data:
                            {
                                data: pickempid,
                                order: orderid,
                                flag: updateFlag
                            },
                            success: function (response)
                            {
                                if (response == 'success')
                                {
                                    $("#cp1" + ord).css("background-color", "green");
                                    $("#cp1" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " CP1 successful");
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function cp1ShuttleUpdate(ord)
        {
            var val = '';
            var flags = 'shuttle';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val,
                    flag: flags
                },
                success: function (response)
                {
                    //alert(response);
                    if (response == 'shuttleTrue')
                    {
                        var pickempid = 'na';
                        var orderid = ord;
                        var updateFlag = 'cp1Shuttle';
                        $.ajax(
                        {
                            type: 'post',
                            url: 'trackerupdate',
                            data:
                            {
                                data: pickempid,
                                order: orderid,
                                flag: updateFlag
                            },
                            success: function (response)
                            {
                                if (response == 'success')
                                {
                                    $("#cp1Shuttle" + ord).css("background-color", "green");
                                    $("#cp1Shuttle" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " CP1 Shuttle successful");
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });            
        }
        function cp2Update(ord)
        {
            var val = '';
            var flags = 'shuttle';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val,
                    flag: flags
                },
                success: function (response)
                {
                    //alert(response);
                    if (response == 'shuttleTrue')
                    {
                        var pickempid = 'na';
                        var orderid = ord;
                        var updateFlag = 'cp2';
                        $.ajax(
                        {
                            type: 'post',
                            url: 'trackerupdate',
                            data:
                            {
                                data: pickempid,
                                order: orderid,
                                flag: updateFlag
                            },
                            success: function (response)
                            {
                                if (response == 'success')
                                {
                                    $("#cp2" + ord).css("background-color", "green");
                                    $("#cp2" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " CP2 successful");
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });               
        }
        function cp2ShuttleUpdate(ord)
        {
            var val = '';
            var flags = 'shuttle';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val,
                    flag: flags
                },
                success: function (response)
                {
                    //alert(response);
                    if (response == 'shuttleTrue')
                    {
                        var pickempid = 'na';
                        var orderid = ord;
                        var updateFlag = 'cp2Shuttle';
                        $.ajax(
                        {
                            type: 'post',
                            url: 'trackerupdate',
                            data:
                            {
                                data: pickempid,
                                order: orderid,
                                flag: updateFlag
                            },
                            success: function (response)
                            {
                                if (response == 'success')
                                {
                                    $("#cp2Shuttle" + ord).css("background-color", "green");
                                    $("#cp2Shuttle" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " CP2 Shuttle successful");
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });               
        }
        function retcp1Update(ord)
        {
            var val = '';
            var flags = 'retcp1';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val,
                    flag: flags
                },
                success: function (response)
                {
                    //alert(response);
                    if (response == 'shuttleTrue')
                    {
                        var pickempid = 'na';
                        var orderid = ord;
                        var updateFlag = 'retcp1';
                        $.ajax(
                        {
                            type: 'post',
                            url: 'trackerupdate',
                            data:
                            {
                                data: pickempid,
                                order: orderid,
                                flag: updateFlag
                            },
                            success: function (response)
                            {
                                if (response == 'success')
                                {
                                    $("#retcp1" + ord).css("background-color", "red");
                                    $("#retcp1" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text(ord + " Return to CP1 successful");
                                } else
                                {
                                    $("#altermsg").css("color", "yellow");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        })
                      } else 
                      {
                          alert("You are unauthorized");
                      } 
                 }
            });
        }
        function DP2Update(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = 'na';
                        var orderid = ord;
                        var updateFlag = 'DP2';
                        $.ajax({
                            type: "POST",
                            url: "trackerupdate",
                            data:
                                {
                                    data: pickempid,
                                    order: orderid,
                                    flag: updateFlag
                                },
                            success: function (msg)
                            {
                                if (msg == 'success')
                                {
                                    $("#DP2" + ord).css("background-color", "green");
                                    $("#DP2" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " DP2 successful");
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function assignDropExec(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = ($("#dropemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'assignDrop';
                        if (pickempid == '')
                        {
                            alert("Please select Drop Executive to assign.");
                        } else
                        {
                            $.ajax({
                                type: "POST",
                                url: "trackerupdate",
                                data:
                                    {
                                        data: pickempid,
                                        order: orderid,
                                        flag: updateFlag
                                    },
                                success: function (msg)
                                {
                                    if (msg == 'success')
                                    {
                                        $("#dropemp" + ord).attr("disabled", true);
                                        $("#assignDrop" + ord).attr("disabled", true);
                                        $("#altermsg").css("color", "green");
                                        $("#altermsg").text(ord + " Assigned Drop Executive successfully");
                                    } else
                                    {
                                        $("#altermsg").css("color", "red");
                                        $("#altermsg").text("Unable to update status");
                                    }
                                }
                            });
                        }
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function PickDropUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                //
                var pickempid = ($("#dropemp" + ord).val());
                var orderid = ord;
                var updateFlag = 'PickDrop';
                $.ajax({
                    type: "POST",
                    url: "trackerupdate",
                    data:
                        {
                            data: pickempid,
                            order: orderid,
                            flag: updateFlag
                        },
                    success: function (msg)
                    {
                        if (msg == 'success')
                        {
                            $("#PickDrop" + ord).css("background-color", "green");
                            $("#PickDrop" + ord).attr("disabled", true);
                            $("#altermsg").css("color", "green");
                            $("#altermsg").text(ord + " Pick successful");
                            $("#Cust" + ord).attr("disabled", false);
                        } else
                        {
                            $("#altermsg").css("color", "red");
                            $("#altermsg").text("Unable to update status");
                        }
                    }
                });
                //
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        function CustUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                //
                var pickempid = ($("#dropemp" + ord).val());
                var orderid = ord;
                var updateFlag = 'Cust';
                $.ajax({
                    type: "POST",
                    url: "trackerupdate",
                    data:
                        {
                            data: pickempid,
                            order: orderid,
                            flag: updateFlag
                        },
                    success: function (msg)
                    {
                        if (msg == 'success')
                        {
                            $("#Cust" + ord).css("background-color", "green");
                            $("#Cust" + ord).attr("disabled", true);
                            $("#altermsg").css("color", "green");
                            $("#altermsg").text(ord + " Cust successful");
                            $("#Cash" + ord).attr("disabled", false);
                            $("#Ret" + ord).attr("disabled", false);
                            $("#NoShow" + ord).attr("disabled", false);
                            $("#Rea" + ord).attr("disabled", false);
                        } else
                        {
                            $("#altermsg").css("color", "red");
                            $("#altermsg").text("Unable to update status");
                        }
                    }
                });
                //
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        function CashUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                var cashModal = document.getElementById('cashModal');
                var span = document.getElementsByClassName("close")[2];
                $('#ordID').css('color','#16469E');
                $('#ordID').html('<b>ORDER ID: </b>'+ord);
                $('#merchantRef').css('color', '#16469E');
                $('#merchantRef').html('<b>MERCHANT Ref: </b>' + $('#dropMerchantRef' + ord).html());
                $('#price').css('color', '#16469E');
                $('#price').html('<b>Package Price: </b>' + $('#price1' + ord).html());
                //$('#cashCollection').val($('#price2' + ord).html());
                cashModal.style.display = "block";
                span.onclick = function ()
                {
                    cashModal.style.display = "none";
                }
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        $('#btnCashSave').click(function ()
        {
            if ($('#op1').is(":checked"))
            {
                var tranType = $('#op1').val();
            } else
            {
                var tranType = $('#op2').val();
            }
            var cashAmount = $('#cashCollection').val();
            var cashRem = $('#cashComment').val();
            var ord = $('#ordID').html().substring(17,35);
            var pickempid = $("#dropemp" + ord).val();
            var orderid = ord;
            var updateFlag = 'Cash';
            $.ajax({
                type: "POST",
                url: "trackerupdate",
                data:
                    {
                        data: pickempid,
                        order: orderid,
                        flag: updateFlag,
                        CashAmt: cashAmount,
                        cashComment: cashRem,
                        cashType: tranType
                    },
                success: function (msg)
                {
                    if (msg == 'success')
                    {
                        $("#Cash" + ord).css("background-color", "green");
                        $("#Cash" + ord).attr("disabled", true);
                        $("#Ret" + ord).attr("disabled", true);
                        $("#partial" + ord).attr("disabled", true);
                        $("#altermsg").css("color", "green");
                        $("#altermsg").text(ord + " Cash update successful");
                        $("#DropDP2" + ord).attr("disabled", false);
                        $("#Rea" + ord).attr("disabled", true);
                        setTimeout($('#cashModal').css('display', 'none'), 2000);
                    } else
                    {
                        $("#altermsg").css("color", "red");
                        $("#altermsg").text(msg);
                        setTimeout($('#cashModal').css('display', 'none'), 2000);
                    }
                }
            });
        })
        $('#btnCashCancel').click(function()
        {
            var cashModal = document.getElementById('cashModal');
            cashModal.style.display = "none";
        })
        function partialUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                var partialModal = document.getElementById('partialModal');
                var span = document.getElementsByClassName("close")[3];
                $('#ordIDpartial').css('color','#16469E');
                $('#ordIDpartial').html('<b>ORDER ID: </b>'+ord);
                $('#merchantRefPartial').css('color', '#16469E');
                $('#merchantRefPartial').html('<b>MERCHANT Ref: </b>' + $('#dropMerchantRef' + ord).html());
                $('#pricePartial').css('color', '#16469E');
                $('#pricePartial').html('<b>Package Price: </b>' + $('#price1' + ord).html());
                partialModal.style.display = "block";
                span.onclick = function ()
                {
                    partialModal.style.display = "none";
                }
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        $('#btnPartialSave').click(function ()
        {
            if ($('#op3').is(":checked"))
            {
                var tranType = $('#op3').val();
            } else
            {
                var tranType = $('#op3').val();
            }
            var partialAmount = $('#partialCollection').val();
            var partialRem = $('#partialReason').val();
            var ord = $('#ordIDpartial').html().substring(17, 35);
            var pickempid = $("#dropemp" + ord).val();
            var orderid = ord;
            var dQty = $('#receivedQty').val();
            var rQty = $('#returnedQty').val();
            var updateFlag = 'partial';
            if (dQty > 0 && rQty > 0)
            {
                //alert(orderid+" || "+pickempid+" || "+tranType+" || "+dQty+" || "+rQty+" || "+partialAmount+" ||"+partialRem+" || "+updateFlag);
                $.ajax({
                    type: "POST",
                    url: "trackerupdate",
                    data:
                        {
                            data: pickempid,
                            order: orderid,
                            flag: updateFlag,
                            partialAmt: partialAmount,
                            partialReason: partialRem,
                            deliveredQty: dQty,
                            returnedQty: rQty,
                            cashType: tranType
                        },
                    success: function (msg)
                    {
                        if (msg == 'success')
                        {
                            $("#partial" + ord).css("background-color", "#ff6a00");
                            $("#partial" + ord).attr("disabled", true);
                            $("#Cash" + ord).attr("disabled", true);
                            $("#Ret" + ord).attr("disabled", true);
                            $("#altermsg").css("color", "#ff6a00");
                            $("#altermsg").text(ord + " Partial update successful");
                            $("#DropDP2" + ord).attr("disabled", false);
                            $("#Rea" + ord).attr("disabled", true);
                            setTimeout($('#partialModal').css('display', 'none'), 2000);
                        } else
                        {
                            $("#altermsg").css("color", "red");
                            $("#altermsg").text(msg);
                            setTimeout($('#partialModal').css('display', 'none'), 2000);
                        }
                    }
                });
            } else 
            {
                alert('Delivered Quantity and Returned Quantity Required');
            }
        })
        $('#btnPartialCancel').click(function()
        {
            var partialModal = document.getElementById('partialModal');
            partialModal.style.display = "none";
        })
        function isNumberKey(inpt)
        {
            var regex = /[^0-9.]/g;
            inpt.value = inpt.value.replace(regex, "");
        }
        function DropDP2Update(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = ($("#dropemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'DropDP2';
                        $.ajax({
                            type: "POST",
                            url: "trackerupdate",
                            data:
                                {
                                    data: pickempid,
                                    order: orderid,
                                    flag: updateFlag
                                },
                            success: function (msg)
                            {
                                if (msg == 'success')
                                {
                                    $("#DropDP2" + ord).css("background-color", "green");
                                    $("#DropDP2" + ord).attr("disabled", true);
                                    $("#Rea" + ord).attr("disabled", true);
                                    $("#Cash" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " DP2 Receive successful");
                                    $("#bank" + ord).attr("disabled", false);
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function BankUpdate(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'usertovalid',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = ($("#dropemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'bank';
                        $.ajax({
                            type: "POST",
                            url: "trackerupdate",
                            data:
                                {
                                    data: pickempid,
                                    order: orderid,
                                    flag: updateFlag
                                },
                            success: function (msg)
                            {
                                if (msg == 'success')
                                {
                                    $("#bank" + ord).css("background-color", "green");
                                    $("#bank" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " Cash & Bank successful");
                                    $("#close" + ord).attr("disabled", false);
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function CloseUpdate(ord)
        {
            var val = '';
            $.ajax({
                type: 'post',
                url: 'closePriv',
                data: {
                    userCheck: val
                },
                success: function (response)
                {
                    if (response == 'empTrue')
                    {
                        //
                        var pickempid = ($("#pickemp" + ord).val());
                        var orderid = ord;
                        var updateFlag = 'close';
                        $.ajax({
                            type: "POST",
                            url: "trackerupdate",
                            data:
                                {
                                    data: pickempid,
                                    order: orderid,
                                    flag: updateFlag
                                },
                            success: function (msg)
                            {
                                if (msg == 'success')
                                {
                                    $("#close" + ord).css("background-color", "green");
                                    $("#close" + ord).attr("disabled", true);
                                    $("#altermsg").css("color", "green");
                                    $("#altermsg").text(ord + " Close order successful");
                                    $("#Rea" + ord).attr("disabled", true);
                                    $("#ReaRet" + ord).attr("disabled", true);
                                } else
                                {
                                    $("#altermsg").css("color", "red");
                                    $("#altermsg").text("Unable to update status");
                                }
                            }
                        });
                        //
                    } else
                    {
                        alert('You are Unauthorized!!');
                    }
                }
            });
        }
        function RetUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                var retModal = document.getElementById('returnModal');
                var span = document.getElementsByClassName("close")[1];
                retModal.style.display = "block";
                span.onclick = function ()
                {
                    retModal.style.display = "none";
                }
                $('#orderid').val(ord);                
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        $('#btnRetSave').click(function ()
        {
            var pickempid = 'na';
            var returnReason = $('#retReason').val();
            var orderid = $('#orderid').val();
            var returnRemarks = $('#retRemarks').val();
            var updateFlag = 'Ret';
            $.ajax({
                type: 'post',
                url: 'trackerupdate',
                data:
                    {
                        data: pickempid,
                        order: orderid,
                        retReason: returnReason,
                        retRemarks: returnRemarks,
                        flag: updateFlag
                    },
                success: function (response)
                {
                    if (response == 'success')
                    {
                        $("#retMessage").css("color", "green");
                        $('#retMessage').html('Orders Picked Successfully');
                        $("#Ret" + orderid).css("background-color", "red");
                        $("#Ret" + orderid).attr("disabled", true);
                        $("#altermsg").css("color", "red");
                        $("#altermsg").text(orderid + " Order Returned");
                        $("#Cash" + orderid).attr("disabled", true);
                        $("#partial" + orderid).attr("disabled", true);
                        $("#Rea" + orderid).attr("disabled", true);
                        $("#DropDP2" + orderid).attr("disabled", false);
                        
                        $.ajax(
                        {
                            type: 'post',
                            url: 'sendMessage',
                            data:
                            {
                                rootData: orderid,
                                flagreq: updateFlag
                            }, success: function (response)
                            {
                                //alert(response);
                                data = JSON.parse(response);
                                for (rst in data)
                                {
                                    var status = data[rst][0].status;
                                    var mobile = data[rst][0].destination;
                                    if (status == 0)
                                    {
                                        $.ajax(
                                        {
                                            type: 'post',
                                            url: 'sendMessage',
                                            data:
                                            {
                                                rootData: orderid,
                                                status: status,
                                                phone: mobile,
                                                flagreq: 'smsStatus'
                                            },
                                            success: function (response2)
                                            {
                                                response2 = '';
                                            }
                                        })
                                    } else
                                    {
                                        $.ajax(
                                        {
                                            type: 'post',
                                            url: 'sendMessage',
                                            data:
                                            {
                                                rootData: orderid,
                                                status: status,
                                                phone: mobile,
                                                flagreq: 'smsStatus'
                                            },
                                            success: function (response2)
                                            {
                                                response2 = '';
                                            }
                                        })
                                    }
                                }
                            }
                        })
                        setTimeout($('#returnModal').css('display', 'none'), 1000);
                        $('#retRemarks').val('');
                        $('#retMessage').html('');
                    } else
                    {
                        $('#retMessage').html(response);
                    }
                }
            });
        })
        $('#btnRetCancel').click(function()
        {
            var retModal = document.getElementById('returnModal');
            retModal.style.display = "none";            
        })
        function holdUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                var onHoldModal = document.getElementById('onHoldModal');
                var span = document.getElementsByClassName("close")[2];
                onHoldModal.style.display = "block";
                span.onclick = function ()
                {
                    onHoldModal.style.display = "none";
                }
                $('#orderid').val(ord);
                $('#onHoldOrder').html('Order ID: '+ ord);
                $('#onHoldOrderID').val(ord);
                $.ajax(
                {
                    type: 'post',
                    url: 'trackerupdate',
                    data:
                    {
                        data: 'na',
                        order: ord,
                        flag: 'checkOnSchedule'
                    },
                    success: function (response)
                    {
                        
                        if (response == 'scheduled')
                        {
                            $.ajax(
                            {
                                type: 'post',
                                url: 'trackerupdate',
                                data:
                                {
                                    data: 'na',
                                    order: ord,
                                    flag: 'requestForOnHoldDate'
                                },
                                success: function (response)
                                {
                                    $('#onHoldDate').val('');
                                    $('#onHoldDate').val(response);
                                }
                            })
                            $.ajax(
                            {
                                type: 'post',
                                url: 'trackerupdate',
                                data:
                                {
                                    data: 'na',
                                    order: ord,
                                    flag: 'requestForOnHoldReason'
                                },
                                success: function (response)
                                {
                                    $('#onReason').html('');
                                    $('#onReason').html(response);
                                }
                            })
                        } else
                        {
                            $('#onHoldDate').val('');
                            $('#onReason').html('<option value="Customer unreachable">Customer unreachable</option><option value="Customer request">Customer request</option>');
                        }
                    }
                })
                $('#btnDiv').attr('hidden', false);                
            } else
            {
                $.ajax(
                {
                    type: 'post',
                    url: 'trackerupdate',
                    data:
                    {
                        data: 'na',
                        order: ord,
                        flag: 'onHoldMessage'
                    },
                    success: function (response)
                    {
                        alert(response);
                    }
                })
            }
        }
        $('#btnOnHoldSave').click(function ()
        {
            var onHoldOrderID = $('#onHoldOrderID').val();
            var onHoldDate = $('#onHoldDate').val();
            var onReason = $('#onReason').val();
            $.ajax(
            {
                type: 'post',
                url: 'trackerupdate',
                data:
                {
                    data: 'na',
                    order: onHoldOrderID,
                    onHoldDate: onHoldDate,
                    onReason: onReason,
                    flag: 'updateOnHold'
                },
                success: function (response)
                {

                    $("#Rea" + onHoldOrderID).css("background-color", "yellow");
                    $("#Rea" + onHoldOrderID).html($("#onHoldDate").val().substr(0, 2));
                    $("#Rea" + onHoldOrderID).attr("disabled", true);
                    $("#altermsg").css("color", "red");
                    $('#altermsg').html(response);
                    $.ajax(
                    {
                        type: 'post',
                        url: 'sendMessage',
                        data:
                        {
                            rootData: onHoldOrderID,
                            flagreq: 'onHold'
                        }, success: function (response)
                        {
                            //alert(response);
                            data = JSON.parse(response);
                            for (rst in data)
                            {
                                var status = data[rst][0].status;
                                var mobile = data[rst][0].destination;
                                if (status == 0)
                                {
                                    $.ajax(
                                    {
                                        type: 'post',
                                        url: 'sendMessage',
                                        data:
                                        {
                                            rootData: onHoldOrderID,
                                            status: status,
                                            phone: mobile,
                                            flagreq: 'smsStatus'
                                        },
                                        success: function (response2)
                                        {
                                            response2 = '';
                                        }
                                    })
                                } else
                                {
                                    $.ajax(
                                    {
                                        type: 'post',
                                        url: 'sendMessage',
                                        data:
                                        {
                                            rootData: onHoldOrderID,
                                            status: status,
                                            phone: mobile,
                                            flagreq: 'smsStatus'
                                        },
                                        success: function (response2)
                                        {
                                            response2 = '';
                                        }
                                    })
                                }
                            }
                        }
                    })
                }
            })
            var onHoldModal = document.getElementById('onHoldModal');
            onHoldModal.style.display = "none";
        })
        $('#btnOnHoldCancel').click(function()
        {
            var onHoldModal = document.getElementById('onHoldModal');
            onHoldModal.style.display = "none";            
        })
        function NoShowUpdate(ord)
        {
            if (document.getElementById("dropemp" + ord).value == document.getElementById("usercode").value)
            {
                //
                var pickempid = ($("#dropemp" + ord).val());
                var orderid = ord;
                var updateFlag = 'NoShow';
                $.ajax({
                    type: "POST",
                    url: "trackerupdate",
                    data:
                        {
                            data: pickempid,
                            order: orderid,
                            flag: updateFlag
                        },
                    success: function (msg)
                    {
                        if (msg == 'success')
                        {
                            $("#NoShow" + ord).css("background-color", "orange");
                            $("#NoShow" + ord).attr("disabled", true);
                            $("#altermsg").css("color", "orange");
                            $("#altermsg").text(ord + " Customer No Show");
                            $("#Cash" + ord).attr("disabled", true);
                            $("#Ret" + ord).attr("disabled", true);
                            $("#Rea" + ord).attr("disabled", true);
                            $("#DropDP2" + ord).attr("disabled", false);
                        } else
                        {
                            $("#altermsg").css("color", "red");
                            $("#altermsg").text("Unable to update status");
                        }
                    }
                });
                //
            } else
            {
                alert("You are Unauthorized!!");
            }
        }
        function sortTable(table, colid)
        {
            var sortOrder = document.getElementById("col2sort").value;

            tbody = table.find('tbody');

            tbody.find('tr').sort(function (a, b)
            {
                if (sortOrder == 1)
                {
                    $("#img" + colid).attr("src", "image/down.png");
                    $("#col2sort").val("2");
                    return $('#' + colid, a).text().localeCompare($('#' + colid, b).text()); // Ascending order
                } else
                {
                    $("#img" + colid).attr("src", "image/up.png");
                    $("#col2sort").val("1");
                    return $('#' + colid, b).text().localeCompare($('#' + colid, a).text()); // Descending order
                }

            }).appendTo(tbody);
            switch (colid)
            {
                case 1:
                    $("#img2").attr("src", "image/updown1.png");
                    $("#img3").attr("src", "image/updown1.png");
                    $("#img4").attr("src", "image/updown1.png");
                    $("#img5").attr("src", "image/updown1.png");
                    $("#img6").attr("src", "image/updown1.png");
                    break;
                case 2:
                    $("#img1").attr("src", "image/updown1.png");
                    $("#img3").attr("src", "image/updown1.png");
                    $("#img4").attr("src", "image/updown1.png");
                    $("#img5").attr("src", "image/updown1.png");
                    $("#img6").attr("src", "image/updown1.png");
                    break;
                case 3:
                    $("#img1").attr("src", "image/updown1.png");
                    $("#img2").attr("src", "image/updown1.png");
                    $("#img4").attr("src", "image/updown1.png");
                    $("#img5").attr("src", "image/updown1.png");
                    $("#img6").attr("src", "image/updown1.png");
                    break;
                case 4:
                    $("#img1").attr("src", "image/updown1.png");
                    $("#img2").attr("src", "image/updown1.png");
                    $("#img3").attr("src", "image/updown1.png");
                    $("#img5").attr("src", "image/updown1.png");
                    $("#img6").attr("src", "image/updown1.png");
                    break;
                case 5:
                    $("#img1").attr("src", "image/updown1.png");
                    $("#img2").attr("src", "image/updown1.png");
                    $("#img3").attr("src", "image/updown1.png");
                    $("#img4").attr("src", "image/updown1.png");
                    $("#img6").attr("src", "image/updown1.png");
                    break;
                case 6:
                    $("#img1").attr("src", "image/updown1.png");
                    $("#img2").attr("src", "image/updown1.png");
                    $("#img3").attr("src", "image/updown1.png");
                    $("#img4").attr("src", "image/updown1.png");
                    $("#img5").attr("src", "image/updown1.png");
                    break;
            }
        }