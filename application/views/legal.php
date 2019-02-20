<div style="overflow-y: scroll;padding:0 .8em .5em;height: 250px;font-family: Courier,Lucida Sans Typewriter,Lucida Typewriter,monospace;font-size: .9em;background-color: #f9f9f9;border: 3px solid #eee;">
	<h4>Data Protection and Privacy</h4>
	<p>In compliance with the General Data Protection Regulation of the European Union, the data controller, the International Meteor Organization (IMO), international non-profit organization under Belgian Law, registration number 0441.894.089, Jozef Mattheessensstraat 60, 2540 Hove, represented by its President, Francis Verbeeck, president@imo.net, informs you of the following:</p>
	<ul>
		<li>The data provided above, as well as the data updated or added later, provided in connection with your registration for this IMC, and information about the payment of your registration fee are collected and processed solely for the purpose of administering your IMC registration, the proper organization of the conference, informing you about future editions, and making anonymous statistical analyses use¬ful for future IMC organizers. For this, we seek your consent, which is also the legal basis for the processing of your personal data.</li>
		<li>Your personal data will be disclosed to IMO officers in charge of administering IMC registrations, composing the program of the conference, and editing the proceedings, all acting under the authority of the IMO, with exception of your first and last name, affiliation, and country, which are shown in the participants' list at the <a href="<?= PUBLIC_URL; ?>/participants">IMC Website</a>. Your personal data will also be provided to the local organization, as mentioned on the <a href="<?= PUBLIC_URL; ?>">IMC Website</a>, for the proper logistic organization of the conference. This may mean that your personal data is transferred to a third country</li>
		<li>If, for whichever reason, the registration is not accepted by the LOC, all data pertaining to it will be erased. Otherwise, they will be stored for at most ten years.</li>
		<li>You have full access to the data you provided using a link sent to you  via email upon registering, and can update or complete most of these data.</li>
		<li>You have the right to request from the controller via email to <?=MAIN_CONTACT;?> access to and rectification or erasure of personal data or restriction of processing concerning of your personal data or to object to processing as well as the right to data portability.</li>
		<li>You may withdraw the consent we seek for processing your personal data at any time by simple request to the controller via email to <?=MAIN_CONTACT;?> (without affecting the lawfulness of the processing of your personal data based on your consent before its withdrawal).</li>
		<li>Information on registration fee payments included in the IMO's bookkeeping will not be erased since accounting data must be kept according to the applicable accounting regulations. This information is not public however and will not be disclosed unless legally required.</li>
		<li>If you erase, or request the erasing of, personal data, services which depend on these data can and will no longer be provided.</li>
  
		<li class="over16">You have the right to lodge a complaint with a supervisory authority.</li>

	</ul>	
</div>

<div class="under16 hidden">
		<p class="dmt"><strong>Since you are not yet 16 years of age</strong>, the consent we seek must be given by someone holding the parental responsibility over you. Please ask this person to complete the following:</p>
</div>


<div class="checkbox">
	<label>
		<input type="checkbox" name="grdp" value="1" required="" id="grdp">
		By ticking this box, 
		<span class="hh under16">
			I
			<div style="background-color: #f9f9f9;border: 3px solid #eee; padding:.5em 1em .5em; margin: .3em 0;"> 
				<div class="row">
					<div class="col-sm-4">
						 
							<label for="firstname_underage"><?= _('First name'); ?> <span class="req">*</span></label>
							<input type="text" name="firstname_underage" class="form-control" id="firstname_underage" value="<?= isset($input['firstname_underage'])?$input['firstname_underage']:"" ?>" required >
						 
					</div> 
					<div class="col-sm-4">
						 
							<label for="lastname_underage"><?= _('Last name'); ?> <span class="req">*</span></label>
							<input type="text" name="lastname_underage" class="form-control" value="<?= isset($input['lastnam_underage'])?$input['lastname_underage']:"" ?>" id="lastname_underage" required>
					 
					</div>  
					<div class="col-sm-4">
						 
							<label for="email_underage"><?= _('Email'); ?> <span class="req">*</span></label>
							<input type="email" name="email_underage" class="form-control" id="email_underage" value="<?= isset($input['email_underage'])?$input['email_underage']:"" ?>"  required >
						 
					</div>     
				</div>
			</div> 
			certify that I am holder of the parental responsibility over the person identified above, and that I give my consent to the data controller to process his/her personal data in strict compliance with the above conditions. This consent extends to my personal data provided in this authorization which the data controller will keep as proof that due consent has been given and which in case of doubt an IMO officer acting under the authority of the IMO, may use to contact me in verification that this authorization is genuine. Also I understand that any participant under 18 years old must be accompanied by a properly and provably authorized adult.<span>*</span>
		</span> 
		<span class="over16 hh">
			I give consent to the data controller to process my personal data <strong>in strict compliance with the above conditions</strong>.<span>*</span>
		</span>
	</label>
</div>