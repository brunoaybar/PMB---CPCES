<template>
	<div id="listAnim">
		<table >
			<thead>
				<tr>
					<th v-if="action == 'view' || action == 'add'">
						<div v-if="action != 'add'" class="center">
							<i class="fa fa-plus-square" style='cursor: pointer' @click="checkAll"></i>
							&nbsp;
							<i class="fa fa-minus-square" style='cursor: pointer' @click="uncheckAll"></i>
						</div>
					</th>
					<th style="cursor:pointer;" @click="setSort('name')">
						{{ pmb.getMessage("animation", "list_animation_name") }} <i :class="getClassSort('name')"></i>
					</th>
					<th style="cursor:pointer;" @click="setSort('event.rawStartDate')">
						{{ pmb.getMessage("animation", "list_animation_date_start") }} <i :class="getClassSort('event.rawStartDate')"></i>
					</th>
					<th style="cursor:pointer;" @click="setSort('event.rawEndDate')"> <i :class="getClassSort('event.rawEndDate')"></i>
						{{ pmb.getMessage("animation", "list_animation_date_end") }}
					</th>
					<th style="cursor:pointer;" @click="setSort('status.label')"> <i :class="getClassSort('status.label')"></i>
						{{ pmb.getMessage("animation", "list_animation_status") }}
					</th>
					<th style="cursor:pointer;" @click="setSort('type.label')"> <i :class="getClassSort('type.label')"></i>
						{{ pmb.getMessage("animation", "list_animation_types") }}
					</th>
					<th>{{ pmb.getMessage("animation", "list_animation_location") }}</th>
					<th>{{ pmb.getMessage("animation", "list_animation_list_price") }}</th>
					<th>{{ pmb.getMessage("animation", "list_animation_available_place") }}</th>
					<th>{{ pmb.getMessage("animation", "list_animation_reserved_place") }}</th>
					<th v-if="action != 'add'"></th>
					<th v-if="action != 'add'"></th>
					<th v-if="action != 'add'"></th>
				</tr>
			</thead>
			<tbody>
				<template v-if="animations.length">
					<tr v-for="(anim, index) in animations" :key='index' style="cursor: pointer" 
					   @click="view(anim.id)" @mouseover="hover = index " @mouseout="hover = -1 " 
					   :class="[ index%2 == 0 ? 'odd' : 'even', index == hover ? 'surbrillance' : '' ]">
						<td v-if="action == 'view' || action == 'add'" @click.stop="" style="cursor: default">
							<div class="center">
								<template v-if="anim.event.dateExpired">
									<input type="checkbox" disabled="true" :value="anim.id" :title="pmb.getMessage('animation', 'animation_date_expired')"/>
								</template>
								<template v-else-if="anim.allQuotas.animationQuotas.global == 0 || anim.allQuotas.availableQuotas.global > 0 || anim.allowWaitingList == 1">
									<input type="checkbox" :disabled="(action == 'add') ? true : false" v-model="animationsSelected" :value="anim.id"/>
								</template>
								<template v-else>
									<input type="checkbox" :disabled="true" :value="anim.id"/>
								</template>
							</div>
						</td>
						<td>
		 					{{ anim.name }}
						</td>
						<td>
							<template v-if="anim.event">{{ anim.event.startDate }} <template v-if="'00:00' !== anim.event.startHour">{{ anim.event.startHour}}</template></template>
							<template v-else>X</template>
						</td>
						<td>
							<template v-if="anim.event">{{ anim.event.endDate }} <template v-if="'00:00' !== anim.event.endHour">{{ anim.event.endHour}}</template></template>
							<template v-else>X</template>
						</td>
						<td>
							<template v-if="anim.status.label"> {{ anim.status.label }} </template>
							<template v-else> {{ pmb.getMessage("animation", "form_search_no_status") }} </template>
						</td>
						<td>
							<template v-if="anim.type.label"> {{ anim.type.label }} </template>
							<template v-else><!-- On ne devrait pas avoir d'animation sans type --></template>
						</td>
						<td>
							<template v-if="anim.location.length">
								<span v-for="(loc, index) in anim.location">
									{{ loc.locationLibelle }}
									<br v-if="!(index == anim.location.length - 1)">
								</span>
							</template>
							<template v-else>{{ pmb.getMessage("animation", "form_search_no_location") }}</template>
						</td>
						<td>
							<template v-if="anim.prices.length">
								<span v-for="(price, index) in anim.prices">
									<template v-if="null != price.value && undefined != price.value">
										{{ price.name }} : {{ price.value}}
										<br v-if="!(index == anim.prices.length - 1)">
									</template>
								</span>
							</template>
						</td>
						<td @click="view(anim.id)">
							<template v-if='anim.hasChildrens'>
								{{ pmb.getMessage("animation", "form_search_NA") }}
							</template>
							<template v-else-if="anim.allQuotas.animationQuotas.global ">
								<template v-if="typeof anim.allQuotas.availableQuotas.global !== 'undefined' ">
									{{ anim.allQuotas.availableQuotas.global }} / {{ anim.allQuotas.animationQuotas.global  }} 
								</template>
							</template>
							<template v-else>
								{{ pmb.getMessage("animation", "form_search_illimited_quotas") }}
							</template>
						</td>
						<td @click="view(anim.id)">
							<template v-if='anim.hasChildrens'>
								{{ pmb.getMessage("animation", "form_search_NA") }}
							</template>
							<template v-else-if="anim.allQuotas.reserved.global >= 0 || anim.allQuotas.reserved.internet >= 0">
									{{ anim.allQuotas.reserved.global + anim.allQuotas.reserved.internet }}  
							</template>
						</td>
						<td v-if="action != 'add'" @click.stop="" class="center">
						<template v-if="(anim.allQuotas.availableQuotas.global > 0 && anim.allQuotas.animationQuotas.global && anim.hasChildrens === false) || (anim.allQuotas.animationQuotas.global == 0 && anim.hasChildrens === false) || (anim.allowWaitingList == 1 && anim.hasChildrens === false)">
								<div class="center">
									<i class="fa fa-user-plus" @click="addRegistration(anim.id)" :title="pmb.getMessage('animation', 'animation_add_registration')"></i>
								</div>
							</template>
						</td>
						<td v-if="action != 'add'" @click.stop="" class="center">
							<div class="center">
								<i class="fa fa-clone" @click="duplicatRegistration(anim.id)" :title="pmb.getMessage('animation', 'animation_duplicate')"></i>
							</div>
						</td>
						<td v-if="action != 'add'" @click.stop="" class="center">
							<div class="center">
								<i class="fa fa-envelope" @click="mailing(anim)" :title="pmb.getMessage('animation', 'animation_go_mailing')"></i>
							</div>
						</td>
					</tr>
				</template>
				<template v-else-if="action != 'list'">
					<tr>
						<td colspan="10" style='text-align: center'>{{ pmb.getMessage("animation", "animation_no_linked_animation") }}</td>
					</tr>
				</template>
				<template v-else>
					<tr>
						<td colspan="10" style='text-align: center'>{{ pmb.getMessage("animation", "animation_no_coming_animation") }}</td>
					</tr>
				</template>
			</tbody>
			
		</table>
		<div class='row'>
	   		<input v-if="action == 'list'" @click="newType" class="bouton" type="button" :value="pmb.getMessage('animation', 'list_animation_addAnimation')"/>
	   		<input v-if="action == 'view' && this.animations.length && this.animationsSelected.length > 0" @click="addRegistration" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_add_registration')"/>
	   		<input v-else-if="action == 'view' && this.animations.length" @click="addRegistration" class="bouton disabled" disabled="" type="button" :value="pmb.getMessage('animation', 'animation_add_registration')"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["animations", "pmb", "action", "formdata"],
		
		data: function () {
			return {
				hover : -1,
				animationsSelected : [],
				filter : {
					name: '',
					sort: '',
					status: ['0'],
					locations: ['0']
				},
			}
		},
		created :function(){
			if (this.formdata && this.formdata.animationsSelected){
				this.animationsSelected = this.formdata.animationsSelected;
			}
		},			
		methods: {
			newType : function() {
				document.location = './animations.php?categ=animations&action=add';
			},
			
			view : function(id) {
				document.location = './animations.php?categ=animations&action=view&id=' + id;
			},
			mailing : function(animation) {
				//on teste s'il y a des inscrits
				if (animation.allQuotas.reserved.global || animation.allQuotas.reserved.internet){
					document.location = './animations.php?categ=animations&action=mailing&id=' + animation.id;
				} else {
					alert(this.pmb.getMessage('animation', 'animation_mailing_no_registred_persons'));
				}	
			},
			addRegistration : function(id) {
				let link = './animations.php?categ=registration&action=add&numAnimation=' + id;
				if (id instanceof Event) {
					// On a pas d'id en param�tre, on regarde donc les animations selectionn�es
					if (this.animationsSelected.length) {
						link = './animations.php?categ=registration&action=add&numAnimation=' + this.animations[0].numParent + '&numDaughtersAnimation=' + this.animationsSelected.join(',');
					} else {
						link = './animations.php?categ=registration&action=add&numAnimation=' + this.animations[0].numParent;
					}
				}
				document.location = link;
			},
			
			checkAll : function() {
				for (let i = 0; i < this.animations.length; i++) {
					if (!this.animationsSelected.includes(this.animations[i].id)) {
						this.animationsSelected.push(this.animations[i].id);
					}
				}
			},
			
			uncheckAll : function() {
				this.animationsSelected = [];
			},
			
			duplicatRegistration : function(id){
				document.location = './animations.php?categ=animations&action=duplicate&id=' + id;
			},
			setSort : function (sortName) {
				this.filter.name = sortName;
				
				if (this.filter.sort == "asc") {
					this.filter.sort = "desc";
				} else {
					this.filter.sort = "asc";
				}
				
				var sortTab = sortName.split('.');

				// TODO : Arriver � gerer le filter pour aller chercher dans des sous objets
				var sort = this.filter.sort;
				this.animations.sort(function (a, b) {
					for (let i = 0; i < sortTab.length; i++) {
						if (a[sortTab[i]]) {
							if (Array.isArray(a[sortTab[i]])) {
								a = a[sortTab[i]][0];
							} else {
								a = a[sortTab[i]];
							}
						} else {
							a = '';
						}
						if (b[sortTab[i]]) {
							if (Array.isArray(b[sortTab[i]])) {
								b = b[sortTab[i]][0];
							} else {
								b = b[sortTab[i]];
							}
						} else {
							b = '';
						}
					}
					if (a.toLowerCase() == b.toLowerCase()) {
						return 0;
					}
					if (sort == "asc") {
						return (a.toLowerCase() < b.toLowerCase()) ? - 1 : 1;
					} else {
						return (a.toLowerCase() > b.toLowerCase()) ? - 1 : 1;
					}
				})
			},
			getClassSort : function (name) {
				var className = "fa fa-sort";
				if (this.filter.name == name) {
					if (this.filter.sort == "asc") {
						className += "-asc";
					} else {
						className += "-desc";
					}
				}
				return className;
			},
		}
	}
</script>