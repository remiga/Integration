<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EspbiXmlEncounter extends Model
{
    public static function createEncounterXml($forXML){
        $SUSIJUSIO_DOK_NR = $forXML['KORTELES_NR'];
        $ATIDARYMO_DATA = $forXML['ATIDARYMO_DATA'];
        $GYDYTOJAS = $forXML['PRACTITIONER'];
        $PACIENTAS = $forXML['PATIENT'];

        $edit = '<feed xmlns="http://www.w3.org/2005/Atom">
   <entry>
      <id>cid:visit.1</id>
      <content type="text/xml">
         <Encounter xmlns="http://hl7.org/fhir">
            <extension url="http://esveikata.lt/Profile/ltnhr-encounter#serviceType">
               <valueCodeableConcept>
                  <coding>
                     <system value="http://esveikata.lt/classifiers/Encounter/ServiceType"/>
                     <code value="planned"/>
                     <display value="Planinė pagalba"/>
                  </coding>
               </valueCodeableConcept>
            </extension>
            <extension url="http://esveikata.lt/Profile/ltnhr-encounter#emiNumber">
               <valueString value="'.$SUSIJUSIO_DOK_NR.'"/>
            </extension>
            <extension url="http://esveikata.lt/Profile/ltnhr-encounter#insurance">
              <extension url="http://esveikata.lt/Profile/ltnhr-encounter#insurance.insured">
                  <valueBoolean value="true"/>
               </extension>
               <extension url="http://esveikata.lt/Profile/ltnhr-encounter#insurance.insuranceAssertedAt">
                  <valueDateTime value="'.$ATIDARYMO_DATA.'+02:00"/>
               </extension>
               </extension>
            <status value="in progress"/>
            <class value="ambulatory"/>
            <subject>
               <reference value="'.$PACIENTAS.'"/>
            </subject>
            <participant>
               <type>
                  <coding>
                     <system value="http://hl7.org/fhir/participant-type"/>
                     <code value="ADM"/>
                  </coding>
               </type>
               <individual>
                  <reference value="'.$GYDYTOJAS.'"/>
               </individual>
            </participant>
            <participant>
               <type>
                  <coding>
                     <system value="http://hl7.org/fhir/participant-type"/>
                     <code value="ATND"/>
                  </coding>
               </type>
               <individual>
                  <reference value="'.$GYDYTOJAS.'"/>
               </individual>
            </participant>
            <period>
               <start value="'.$ATIDARYMO_DATA.'+02:00"/>
            </period>
            <serviceProvider>
               <reference value="Organization/1000098802"/>
            </serviceProvider>
         </Encounter>
      </content>
   </entry>
   <entry>
      <id>cid:visit_ESPBI_Provenance.1_OPEN</id>
      <content type="text/xml">
         <Provenance xmlns="http://hl7.org/fhir">
            <target>
               <reference value="cid:visit.1"/>
            </target>
            <recorded value="'.$ATIDARYMO_DATA.'+02:00"/>
            <reason>
               <coding>
                  <system value="http://esveikata.lt/classifiers/EventType"/>
                  <code value="1"/>
                  <display value="Atvykimas į SPĮ gauti ambulatorinių paslaugų"/>
               </coding>
            </reason>
            <agent>
               <role>
                  <system value="http://hl7.org/fhir/provenance-participant-role"/>
                  <code value="author"/>
               </role>
               <type>
                  <system value="http://hl7.org/fhir/provenance-participant-type"/>
                  <code value="practitioner"/>
               </type>
               <reference value="'.$GYDYTOJAS.'"/>
            </agent>
         </Provenance>
      </content>
   </entry>
</feed>';
        return $edit;
    }
}
